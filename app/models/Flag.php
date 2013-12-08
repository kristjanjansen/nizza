<?php

class Flag extends Eloquent {

  protected $table = 'flag';
  
  public function user()
  {
      return $this->belongsTo('User');
  }  

}