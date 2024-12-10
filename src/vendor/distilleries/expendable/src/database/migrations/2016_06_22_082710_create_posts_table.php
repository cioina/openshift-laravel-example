<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->uuid('cache_key');
            $table->string('label', 300);
            $table->integer('facebook_image_id')->unsigned()->default(0);
            $table->text('content');
            $table->boolean('is_raw')->unsigned()->default(0);
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
        Schema::drop('posts');
    }
}
