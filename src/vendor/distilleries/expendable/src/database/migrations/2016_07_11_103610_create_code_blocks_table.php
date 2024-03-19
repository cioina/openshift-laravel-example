<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCodeBlocksTable extends Migration {

    /**
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('code_blocks', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('label', 300);
            $table->string('code_type', 30);
            $table->text('code_block');
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
        Schema::drop('code_blocks');
    }
}
