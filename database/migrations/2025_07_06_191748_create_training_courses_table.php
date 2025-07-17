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
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // title: VARCHAR(255), NN
            $table->text('description'); // description: TEXT, NN
            $table->string('trainer_name')->nullable(); // title: VARCHAR(255), NN
            $table->date('start_date')->nullable(); // start_date: DATE, NULLABLE
            $table->date('end_date')->nullable(); // end_date: DATE, NULLABLE
            $table->unsignedInteger('duration_hours')->nullable();
            $table->string('location', 255)->nullable(); // مكان الدورة
            $table->boolean('is_announced')->default(false);
            $table->integer('max_volunteers')->default(0)->nullable();
            $table->integer('current_volunteers')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_courses');
    }
};
