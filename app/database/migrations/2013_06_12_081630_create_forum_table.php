<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateForumTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum', function(Blueprint $table) {
            $table->increments('id');	
            $table->integer('user_id')->index();	
            $table->string('title');
            $table->text('body');
            $table->string('forum_type')->index();
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
        Schema::drop('forum');
    }

}
