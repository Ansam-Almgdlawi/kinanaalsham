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
        Schema::create('event_types', function (Blueprint $table) {
            // المعرف الأساسي
            $table->id(); // BIGINT UNSIGNED, PK, AI, NN

            // اسم نوع الفعالية (يجب أن يكون فريداً)
            $table->string('name', 100)->unique(); // VARCHAR(100), UNIQUE, NN

            // الوصف (اختياري)
            $table->text('description')->nullable(); // TEXT, NULLABLE

            // التواريخ التلقائية
            $table->timestamp('created_at')->nullable()->useCurrent(); // TIMESTAMP, DEFAULT CURRENT_TIMESTAMP
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate(); // TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE

            // إضافة ملاحظة للجدول (اختياري)
            $table->comment('جدول أنواع الفعاليات (تدريب، فعاليات مجتمعية، إلخ)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
