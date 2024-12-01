<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGuestEmailsTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_emails', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('email', 80);
            $table->string('email_subject', 100);
            $table->string('email_body', 700);
            $table->string('first_name', 40);
            $table->string('last_name', 40);

            $table->string('phone', 15)->nullable();
            $table->string('zip', 9)->nullable();
            $table->char('gender', 1)->nullable();

            $table->char('country_code', 3)->nullable();
            $table->foreign('country_code')->references('country_code')->on('countries');

            $table->char('state_code', 2)->nullable();
            $table->foreign('state_code')->references('state_code')->on('us_states');

            $table->integer('age')->unsigned()->nullable();
            $table->date('birthday')->nullable();

            $table->char('request_id', 32)->nullable();
            $table->string('request_ip_address', 20)->nullable();
            $table->boolean('has_facebook')->unsigned()->default(0);
            $table->boolean('is_facebook')->unsigned()->default(0);
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
        Schema::drop('guest_emails');
    }
}
