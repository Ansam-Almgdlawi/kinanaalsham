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
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, PK, AI, NN

            $table->string('project_number', 50)->unique(); // VARCHAR(50), UNIQUE, NN
            $table->string('name', 255); // VARCHAR(255), NN
            $table->text('description')->nullable(); // TEXT, NULLABLE
            $table->decimal('budget', 15, 2); // DECIMAL(15,2), NN
            $table->text('objective'); // TEXT, NN
            $table->enum('status', [
                'planning',
                'active',
                'on_hold',
                'completed',
                'cancelled'
            ])->default('planning'); // ENUM, NN, DEFAULT "planning"

            $table->date('start_date')->nullable(); // DATE, NULLABLE
            $table->date('end_date')->nullable(); // DATE, NULLABLE
            $table->text('requirements')->nullable(); // TEXT, NULLABLE
            $table->Integer('max_beneficiaries')->default(0);
            $table->Integer('current_beneficiaries')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0.00); // DECIMAL(15,2), NN, DEFAULT 0.00
            $table->decimal('total_expenses', 15, 2)->default(0.00); // DECIMAL(15,2), NN, DEFAULT 0.00

            $table->unsignedBigInteger('created_by_user_id'); // BIGINT UNSIGNED, FK
            $table->foreign('created_by_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
