<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebPageImagesTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_page_images', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('web_page_id')->unsigned();
            $table->foreign('web_page_id')->references('id')->on('web_pages');
            $table->integer('image_id')->unsigned();
            $table->foreign('image_id')->references('id')->on('facebook_images');
            $table->unique(['web_page_id', 'image_id']);
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
        Schema::drop('web_page_images');
    }
}
