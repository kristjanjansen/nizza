<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment', function(Blueprint $table) {
          $table->increments('id');	
          $table->integer('user_id')->index();	
          $table->integer('commentable_id')->index();	
          $table->string('commentable_type')->index();	
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
        Schema::drop('comment');
    }

}
