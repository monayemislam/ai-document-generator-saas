<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:proposal,invoice',
            'client_name' => 'required',
            'project_name' => 'required',
            'budget' => 'required',
            'timeline' => 'required',
            'scope' => 'required'
        ]);

        try {
            $content = $this->aiService->generateDocument($validated['type'], $validated);

            if ($content) {
                // Save to database based on type
                if ($validated['type'] === 'proposal') {
                    $project = Project::where('name', $validated['project_name'])
                        ->whereHas('client', function($query) use ($validated) {
                            $query->where('name', $validated['client_name']);
                        })->first();

                    if ($project) {
                        $project->proposals()->create([
                            'content' => $content,
                            'status' => 'draft'
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'content' => $content
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
