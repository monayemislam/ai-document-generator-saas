<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Models\Client;


class DocumentController extends Controller
{
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            // Project Details
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'priority_level' => 'required|in:low,medium,high',
            'project_type' => 'required|in:consulting,design,development',
            'project_scope' => 'required|string',

            // Pricing
            'hourly_rate' => 'required_without:fixed_price|nullable|numeric|min:0',
            'fixed_price' => 'required_without:hourly_rate|nullable|numeric|min:0',
            'estimated_hours' => 'nullable|numeric|min:0',
            'items' => 'nullable|json',

            // Payment Terms
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer',
            'payment_due_date' => 'required|date',
            'payment_schedule' => 'required|in:upon_receipt,net_15,net_30',

            // Additional Terms
            'cancellation_policy' => 'required|string',
            'revision_policy' => 'required|string',
            'terms_agreed' => 'required|accepted',
            'custom_message' => 'nullable|string',

            // Branding
            'logo' => 'nullable|image|max:2048', // 2MB max
            'color_scheme' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = Client::findOrFail($request->client_id);
            
            $document = Document::create([
                'user_id' => auth()->id(),
                'client_id' => $client->id,
                'client_name' => $client->name,
                'company_name' => $client->company_name,
                'contact_person' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'street_address' => $client->address,
                'city' => $client->city,
                'state' => $client->state,
                'zip_code' => $client->postal_code,
                'country' => $client->country,
                'project_title' => $request->project_title,
                'project_description' => $request->project_description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority_level' => $request->priority_level,
                'project_type' => $request->project_type,
                'hourly_rate' => $request->hourly_rate,
                'fixed_price' => $request->fixed_price,
                'estimated_hours' => $request->estimated_hours,
                'payment_method' => $request->payment_method,
                'payment_due_date' => $request->payment_due_date,
                'payment_schedule' => $request->payment_schedule,
                'cancellation_policy' => $request->cancellation_policy,
                'revision_policy' => $request->revision_policy,
                'terms_agreed' => $request->terms_agreed,
                'custom_message' => $request->custom_message,
                'logo_path' => null,
                'color_scheme' => $request->color_scheme,
                'project_scope' => $request->project_scope,
            ]);

            // Handle items if present
            if ($request->items) {
                $items = json_decode($request->items, true);
                foreach ($items as $item) {
                    $document->items()->create([
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_cost' => $item['quantity'] * $item['unit_price']
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Document generated successfully',
                'document_id' => $document->id,
                'redirect' => route('documents.show', $document->id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        $document->load('items');
        
        // Get the full URL for the logo
        $logoUrl = null;
        if ($document->logo_path) {
            $logoUrl = Storage::disk('public')->url($document->logo_path);
        }
        
        return Inertia::render('Documents/Show', [
            'document' => $document,
            'logoUrl' => $logoUrl
        ]);
    }

    /**
     * Display a listing of the documents.
     */
    public function index(Request $request)
    {
        $documents = Document::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);
        
        return Inertia::render('Documents/Index', [
            'documents' => $documents
        ]);
    }

    public function destroy(Document $document)
    {
        // Delete the logo file if it exists
        if ($document->logo_path) {
            Storage::disk('public')->delete($document->logo_path);
        }

        // Delete the document (related items will be deleted via cascade)
        $document->delete();

        return redirect()->route('documents.index')
            ->with('message', 'Document deleted successfully');
    }

    public function downloadPdf(Document $document)
    {
        if (Gate::denies('view', $document)) {
            abort(403);
        }

        try {
            $pdf = Pdf::loadView('pdf.document', [
                'document' => $document,
                'date' => now()->format('F d, Y')
            ])->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultPaperSize' => 'a4',
            ]);

            // Force download the PDF
            return response($pdf->output())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $document->project_title . '.pdf"');

        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF');
        }
    }

    public function create()
    {
        return Inertia::render('Documents/Create', [
            'clients' => auth()->user()->clients()->select('id', 'name', 'company_name')->get()
        ]);
    }
}
