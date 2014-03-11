<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFlightTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight', function(Blueprint $table) {
            $table->increments('id');	
            $table->integer('user_id')->index();	
            $table->string('title');
            $table->text('body');
            $table->integer('carrier_id')->index();	
            $table->string('url');
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
        Schema::drop('flight');
    }

}
