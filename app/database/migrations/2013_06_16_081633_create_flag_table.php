<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFlagTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flag', function(Blueprint $table) {
            $table->increments('id');	
            $table->integer('user_id')->index();	
            $table->integer('flaggable_id')->index();	
            $table->string('flaggable_type')->index();	
            $table->string('flag_type')->index();	
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
        Schema::drop('flag');
    }

}
