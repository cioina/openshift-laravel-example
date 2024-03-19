<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveysTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('person_name', 100);
            $table->boolean('is_complex_name')->unsigned()->default(0);
            $table->string('complex_name', 200)->nullable();
            $table->string('email', 80)->nullable();
            $table->char('gender', 1)->nullable();
            $table->date('birthday')->nullable();
 
            $table->char('country_code', 3)->nullable();
            $table->foreign('country_code')->references('country_code')->on('countries');
            $table->char('state_code', 2)->nullable();
            $table->foreign('state_code')->references('state_code')->on('us_states');
            $table->string('phone', 15)->nullable();
            $table->string('zip', 9)->nullable();

            $table->string('recent_vacation', 500)->nullable();
            $table->integer('first_drive_age')->unsigned()->nullable();

            $table->string('ip_address', 20);
            $table->char('session_id', 32);

            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients');

            $table->timestamps();
        });
    }

    /**
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('surveys');
    }
}
