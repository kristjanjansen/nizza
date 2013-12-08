<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content', function(Blueprint $table) {
            $table->increments('id');	
            $table->integer('user_id')->index();	
            $table->string('title');
            $table->string('type');
            $table->text('body');
            $table->timestamps();
      			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('content');
    }

}
