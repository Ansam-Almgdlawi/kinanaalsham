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
        Schema::create('course_volunteer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('training_courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('user_type', 20)
                ->comment('volunteer أو beneficiary');
            $table->string('status')->default('pending');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']); // لمنع التسجيل المكرر
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_volunteer_pivot');
    }
};
