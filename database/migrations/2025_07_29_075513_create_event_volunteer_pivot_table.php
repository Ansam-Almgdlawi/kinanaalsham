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
        Schema::create('event_volunteer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('user_type', 20)
                ->comment('volunteer (متطوع) أو beneficiary (مستفيد) أو admin (مسؤول)');
            $table->enum('status', ['pending', 'approved', 'rejected', 'attended', 'absent'])
                ->default('pending')
                ->comment('حالة التسجيل: pending (قيد الانتظار), approved (مقبول), rejected (مرفوض), attended (حاضر), absent (غائب)');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            // لمنع التسجيل المكرر لنفس المستخدم في نفس الفعالية
            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_volunteer');
    }
};
