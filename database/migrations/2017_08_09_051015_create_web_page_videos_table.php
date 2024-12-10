<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebPageVideosTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_page_videos', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('web_page_id')->unsigned();
            $table->foreign('web_page_id')->references('id')->on('web_pages');
            $table->integer('video_id')->unsigned();
            $table->foreign('video_id')->references('id')->on('youtube_videos');
            $table->unique(['web_page_id', 'video_id']);
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
        Schema::drop('web_page_videos');
    }
}
