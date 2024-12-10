<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSentEmailsTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_emails', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('email_type')->unsigned();
            $table->foreign('email_type')->references('email_type_id')->on('email_types');
            $table->string('sent_to_email', 50);
            $table->char('request_session',41)->nullable();
            $table->string('request_ip_address',20)->nullable();
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
        Schema::drop('sent_emails');
    }
}
