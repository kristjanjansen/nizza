<?php

class Destination extends Eloquent {

  protected $table = 'destination';

  public $timestamps = false;

  public function forum() {
    return $this->belongsToMany('Forum', 'destination_map', 'destination_id', 'content_id');
  }

  public function expat() {
    return $this->belongsToMany('Expat', 'destination_map', 'destination_id', 'content_id');
  }

  public function travelmate() {
    return $this->belongsToMany('Travelmate', 'destination_map', 'destination_id', 'content_id');
  }

  public function image() {
    return $this->belongsToMany('Image', 'destination_map', 'destination_id', 'content_id');
  }
  
  public function flight() {
    return $this->belongsToMany('Flight', 'destination_map', 'destination_id', 'content_id');
  }

  public function offer() {
    return $this->belongsToMany('Offer', 'destination_map', 'destination_id', 'content_id');
  }


}