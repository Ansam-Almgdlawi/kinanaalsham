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
        Schema::create('in_kind_donations', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->enum('category', ['food', 'clothes', 'heating','Medicines']);
            $table->string('item_name');
            $table->integer('quantity')->unsigned();
            $table->string('unit');
            $table->dateTime('donated_at')->default(now());
            $table->enum('status', ['pending', 'added_to_inventory'])->default('pending');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_kind_donations');
    }
};
