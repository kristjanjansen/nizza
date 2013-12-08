<?php

class User extends Eloquent {

  protected $table = 'user';
  
  public function comments() {
      return $this->hasMany('Comment');
  }
 

}