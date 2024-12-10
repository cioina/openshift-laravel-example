<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('username', 50)->unique();
            $table->string('email', 80)->unique();
            $table->string('password', 255);

            $table->string('first_name', 40);
            $table->string('last_name', 40);
                        
            $table->boolean('is_suspended')->unsigned()->default(0);
            $table->boolean('is_deleted')->unsigned()->default(0);

            $table->string('phone', 15)->nullable();
            $table->string('zip', 9)->nullable();
            $table->char('gender', 1)->nullable();
            $table->date('birthday')->nullable();

            $table->char('country_code', 3)->nullable();
            $table->foreign('country_code')->references('country_code')->on('countries');

            $table->char('state_code', 2)->nullable();
            $table->foreign('state_code')->references('state_code')->on('us_states');

            $table->char('request_id', 32)->unique()->nullable();
            $table->char('ac_code', 32)->nullable();
            $table->string('request_ip_address', 20)->nullable();
            $table->boolean('is_remember_username')->unsigned()->default(0);

            $table->string('fb_email', 150)->unique();
            $table->string('fb_first_name', 100);
            $table->string('fb_last_name', 100);
            $table->string('fb_id', 20);
            $table->string('fb_token', 2000);
            $table->string('fb_picture', 2000);
            $table->boolean('fb_verified')->unsigned()->default(0);
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
        Schema::drop('clients');
    }
}
