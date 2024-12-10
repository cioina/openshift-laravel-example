<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFacebookImagesTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_images', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('album_id',16)->nullable();
            $table->string('photo_id',16)->nullable();
            $table->string('small_image_url', 512)->nullable();
            $table->string('original_image_url', 512)->nullable();
            $table->string('label', 300)->nullable();
            $table->text('content')->nullable();
            $table->boolean('status')->unsigned()->default(0);
            $table->boolean('is_expired')->unsigned()->default(0);
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
        Schema::drop('facebook_images');
    }
}
