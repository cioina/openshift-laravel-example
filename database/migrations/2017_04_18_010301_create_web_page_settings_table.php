<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebPageSettingsTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_page_settings', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('web_page_id')->unsigned();
            $table->foreign('web_page_id')->references('id')->on('web_pages');
            $table->integer('setting_id')->unsigned();
            $table->foreign('setting_id')->references('id')->on('settings');
            $table->unique(['web_page_id', 'setting_id']);
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
        Schema::drop('web_page_settings');
    }
}
