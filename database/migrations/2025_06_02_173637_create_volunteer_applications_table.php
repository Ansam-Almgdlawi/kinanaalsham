<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('age');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone_number');
            $table->string('email');
            $table->text('skills');
            $table->enum('interests',
                ['Educational','Medicine','Organizational','Media','technical']);
            $table->json('available_times')->comment('Available days and hours for volunteering');
            $table->enum('status', ['new', 'reviewed', 'accepted', 'rejected'])
                ->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
    }
}
