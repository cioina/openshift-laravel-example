<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebPagesTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_pages', function(Blueprint $table)
        {
            $table->increments('id');
            $table->uuid('cache_key');
            $table->string('label', 300);
            $table->string('slug', 300);
            $table->text('content');
            $table->boolean('is_raw')->unsigned()->default(0);
            $table->boolean('status')->unsigned()->default(0);
            $table->boolean('is_public')->unsigned()->default(0);
            $table->boolean('has_form')->unsigned()->default(0);
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
        Schema::drop('web_pages');
    }
}
