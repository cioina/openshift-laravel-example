<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateYoutubeVideosTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_videos', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('video_type')->unsigned();
            $table->foreign('video_type')->references('video_type_id')->on('video_types');
            $table->string('small_image_url', 512)->nullable();
            $table->string('original_image_url', 512)->nullable();

            $table->string('small_image_quality', 25)->default('default.jpg');
            $table->string('original_image_quality', 25)->default('hqdefault.jpg');
            $table->string('domain', 10)->default('i');

            $table->string('label', 300);
            $table->string('video_id', 25);
            $table->integer('start')->unsigned()->nullable();
            $table->integer('end')->unsigned()->nullable();
            $table->text('content');
            $table->boolean('status')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
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
        Schema::drop('youtube_videos');
    }
}
