<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVideoTypesTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_types', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('video_type_id')->unsigned()->unique();
            $table->string('video_type_name', 40)->unique();
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
        Schema::drop('video_types');
    }
}
