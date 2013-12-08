<?php

class FlightField extends Eloquent {

  protected $table = 'flight';
  public $timestamps = false;

  public function carrier() {
      return $this->belongsTo('Carrier');
  }
  
}