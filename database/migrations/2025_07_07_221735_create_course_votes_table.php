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
        Schema::create('course_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('training_courses'); // أي دورة
            $table->foreignId('user_id')->constrained('users'); // أي متطوع
            $table->timestamp('voted_at')->useCurrent(); // وقت التصويت
            $table->unique(['course_id', 'user_id']); // منع التصويت المكرر
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_votes');
    }
};
