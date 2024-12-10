<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebStatisticsTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_statistics', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->dateTime('request_date')->nullable();
            $table->char('request_session',41)->nullable();
            $table->string('request_ip_address',20)->nullable();
            $table->string('http_user_agent',2000)->nullable();
            $table->string('unique_id',32)->nullable();
            $table->string('absolute_uri',4000)->nullable();
            $table->boolean('is_begin_request')->unsigned()->default(0);
            $table->integer('request_count')->unsigned()->nullable();
            $table->string('referrer',4000)->nullable();
            $table->smallInteger('browser_id')->unsigned()->nullable();
            $table->boolean('is_fake_visitor')->unsigned()->default(0);
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
        Schema::drop('web_statistics');
    }
}
