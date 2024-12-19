<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'client_name',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'street_address',
        'city',
        'state',
        'zip_code',
        'country',
        'project_title',
        'project_description',
        'start_date',
        'end_date',
        'project_scope',
        'priority_level',
        'project_type',
        'hourly_rate',
        'fixed_price',
        'estimated_hours',
        'payment_method',
        'payment_due_date',
        'payment_schedule',
        'cancellation_policy',
        'revision_policy',
        'terms_agreed',
        'custom_message',
        'logo_path',
        'color_scheme',
        'reminder_date',
        'reminder_method'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_due_date' => 'date',
        'reminder_date' => 'date',
        'terms_agreed' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DocumentItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
