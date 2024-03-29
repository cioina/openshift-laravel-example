<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsStatesTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('us_states', function(Blueprint $table)
        {
             $table->char('state_code', 2)->primary();
             $table->string('state_name', 60);
             $table->boolean('is_territory')->unsigned()->default(0);
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
        Schema::drop('us_states');
    }
}
