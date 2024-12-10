<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOnlineClientsTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_clients', function(Blueprint $table)
        {
            $table->increments('id');
            $table->boolean('is_logged_out')->unsigned()->default(0);
            $table->string('ip_address', 20);
            $table->char('session_id', 32);
            $table->char('online_id', 32)->unique();
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
        Schema::drop('online_clients');
    }
}
