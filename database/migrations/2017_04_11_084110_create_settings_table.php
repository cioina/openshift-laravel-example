<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function(Blueprint $table)
        {
            $table->increments('id');
            $table->uuid('cache_key');
            $table->string('label', 300);
            $table->string('code_type', 30);
            $table->text('code_block');
            $table->text('content')->nullable();;
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
        Schema::drop('settings');
    }
}
