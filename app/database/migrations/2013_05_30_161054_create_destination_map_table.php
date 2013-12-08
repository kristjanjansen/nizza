<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDestinationMapTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('destination_map', function(Blueprint $table) {
          $table->increments('id');	
          $table->integer('content_id')->index();	
          $table->integer('destination_id')->index();	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('destination_map');
    }

}
