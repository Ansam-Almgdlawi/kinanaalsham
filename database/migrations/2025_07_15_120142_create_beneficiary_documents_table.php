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
        Schema::create('beneficiary_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_user_id')->constrained('users')->onDelete('cascade');
            $table->string('document_type');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_documents');
    }
};
