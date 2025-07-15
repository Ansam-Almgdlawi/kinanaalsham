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
        Schema::create('beneficiary_details', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->onDelete('cascade');
            $table->foreignId('beneficiary_type_id')->constrained();
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed', 'unknown'])->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->unsignedInteger('family_members_count')->nullable();
            $table->text('case_details')->nullable();
            $table->date('registration_date');
            $table->timestamp('last_document_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_details');
    }
};
