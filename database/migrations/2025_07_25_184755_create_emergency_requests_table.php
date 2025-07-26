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
        Schema::create('emergency_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_user_id')->constrained('users')->onDelete('cascade');
            $table->text('request_details');
            $table->string('address');
            $table->enum('required_specialization', ['طبي', 'ميداني', 'استشاري']);
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'cancelled'])->default('pending');
            $table->foreignId('assigned_volunteer_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_requests');
    }
};
