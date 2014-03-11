<?php

class Flight extends Eloquent {

  protected $table = 'flight';

  public function user() {
      return $this->belongsTo('User');
  }

  public function comments() {
      return $this->morphMany('Comment', 'commentable');
  }
  
  public function destinations()
  {
      return $this->morphToMany('Destination', 'destinationable');
  }
  
  public function carrier() {
      return $this->belongsTo('Carrier');
  }
      
}