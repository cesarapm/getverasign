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
        Schema::create('signature_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_name');
            $table->text('description')->nullable();
            $table->string('pdf_path');
            $table->json('signers');
            $table->json('signature_fields');
            $table->string('signing_order', 20)->default('sequential');
            $table->unsignedSmallInteger('expiration_days')->default(30);
            $table->boolean('notify_on_signing')->default(true);
            $table->boolean('notify_on_rejection')->default(true);
            $table->boolean('notify_on_expiration')->default(true);
            $table->string('status', 30)->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_documents');
    }
};
