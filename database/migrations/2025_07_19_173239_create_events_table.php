<?php

// database/migrations/2025_07_19_000000_create_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, PK, AI, NN (مكافئ لـ 'id' في وصفك)

            $table->string('name', 255); // VARCHAR(255), NN
            $table->text('description'); // TEXT, NN
            $table->unsignedBigInteger('event_type_id')->nullable(); // FK لـ event_types
            $table->dateTime('start_datetime'); // DATETIME, NN
            $table->dateTime('end_datetime'); // DATETIME, NN
            $table->string('location_text', 255)->nullable(); // VARCHAR(255), NULLABLE
            $table->decimal('latitude', 10, 8)->nullable(); // DECIMAL(10, 8), NULLABLE
            $table->decimal('longitude', 11, 8)->nullable(); // DECIMAL(11, 8), NULLABLE

            // ENUM لحالة الفعالية
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])
                ->default('planned');

            // Foreign Keys للمنظم والمشرف
            $table->unsignedBigInteger('organizer_user_id'); // NN (الأدمن المنظم)
            $table->unsignedBigInteger('supervisor_user_id')->nullable(); // NULLABLE

            $table->string('target_audience', 255)->nullable(); // VARCHAR(255), NULLABLE
            $table->unsignedInteger('max_participants')->nullable(); // INT UNSIGNED, NULLABLE
            $table->boolean('is_public')->default(true); // BOOLEAN, DEFAULT TRUE

            // Timestamps
            $table->timestamps(); // created_at و updated_at تلقائيًا

            // Foreign Key Constraints
            $table->foreign('event_type_id')->references('id')->on('event_types');
            $table->foreign('organizer_user_id')->references('id')->on('users');
            $table->foreign('supervisor_user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }

}
