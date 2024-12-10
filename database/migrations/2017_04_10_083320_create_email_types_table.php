<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTypesTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_types', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('email_type_id')->unsigned()->unique();
            $table->string('email_type_name', 40)->unique();
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
        Schema::drop('email_types');
    }
}
