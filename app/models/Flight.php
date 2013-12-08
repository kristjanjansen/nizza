<?php

class Flight extends Content {

  public function field() {
      return $this->hasOne('FlightField', 'content_id');
  }
      
}