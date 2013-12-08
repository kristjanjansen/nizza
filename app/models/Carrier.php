<?php

class Carrier extends Eloquent {

  protected $table = 'carrier';

  public $timestamps = false;
  
  public function forum() {
      return $this->hasOne('Flight');
  }

}