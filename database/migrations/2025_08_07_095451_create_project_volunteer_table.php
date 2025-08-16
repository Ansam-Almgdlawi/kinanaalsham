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
        Schema::create('project_volunteer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('user_type', 20)
                ->comment('volunteer (متطوع) أو beneficiary (مستفيد) أو admin (مسؤول)');
            $table->enum('status', ['pending', 'approved', 'rejected', 'attended', 'absent'])
                ->default('pending')
                ->comment('حالة التسجيل: pending (قيد الانتظار), approved (مقبول), rejected (مرفوض), attended (حاضر), absent (غائب)');
            $table->text('motivation')->nullable()
                ->comment('سبب التطوع أو معلومات إضافية');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            // لمنع التسجيل المكرر لنفس المستخدم في نفس الفعالية
            $table->unique(['project_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_volunteer');
    }
};
