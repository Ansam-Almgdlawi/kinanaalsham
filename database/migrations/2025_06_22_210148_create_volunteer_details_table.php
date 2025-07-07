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
        Schema::create('volunteer_details', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->text('skills')->nullable();
            $table->text('interests')->nullable();
            $table->text('availability_schedule')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->timestamp('date_joined_from_form')->nullable();
            $table->decimal('total_hours_volunteered', 10, 2)->default(0.00);
            $table->enum('volunteering_type_preference', ['remote', 'on_site', 'both'])->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_details');
    }
};
