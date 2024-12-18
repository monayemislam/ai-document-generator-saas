<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            // Client Information
            $table->string('client_name');
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            // Address
            $table->string('street_address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('country');
            // Project Details
            $table->string('project_title');
            $table->text('project_description');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('project_scope');
            $table->enum('priority_level', ['low', 'medium', 'high']);
            $table->enum('project_type', ['consulting', 'design', 'development']);
            // Pricing
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('fixed_price', 10, 2)->nullable();
            $table->integer('estimated_hours')->nullable();
            // Payment Terms
            $table->enum('payment_method', ['credit_card', 'paypal', 'bank_transfer']);
            $table->date('payment_due_date');
            $table->enum('payment_schedule', ['upon_receipt', 'net_15', 'net_30']);
            // Additional Terms
            $table->text('cancellation_policy');
            $table->text('revision_policy');
            $table->boolean('terms_agreed');
            $table->text('custom_message')->nullable();
            // Branding
            $table->string('logo_path')->nullable();
            $table->string('color_scheme')->nullable();
            // Reminders
            $table->date('reminder_date')->nullable();
            $table->enum('reminder_method', ['email', 'sms'])->nullable();
            $table->timestamps();
        });

        // Create table for cost breakdown items
        Schema::create('document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
        });

        // Create table for document attachments
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
