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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->bigIncrements('id'); // id: BIGINT UNSIGNED, PK, AI, NN
            $table->string('title'); // title: VARCHAR(255), NN
            $table->text('description'); // description: TEXT, NN
            $table->enum('type', ['job', 'volunteering']); // type: ENUM("job", "volunteering"), NN
            $table->enum('status', ['open', 'closed', 'filled'])->default('open'); // status: ENUM("open", "closed", "filled"), NN, DEFAULT "open"
            $table->string('location_text')->nullable(); // location_text: VARCHAR(255), NULLABLE
            $table->date('start_date')->nullable(); // start_date: DATE, NULLABLE
            $table->date('end_date')->nullable(); // end_date: DATE, NULLABLE
            $table->text('requirements')->nullable(); // requirements: TEXT, NULLABLE
            $table->boolean('is_remote')->default(false); // is_remote: BOOLEAN, NN, DEFAULT FALSE

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
