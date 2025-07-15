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
            $table->string('target_audience_description', 255)->nullable(); // وصف الجمهور المستهدف
            $table->unsignedBigInteger('created_by_user_id'); // FK إلى users.id
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
