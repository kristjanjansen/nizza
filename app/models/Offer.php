<?php

class Offer extends Content {

  public function field() {
      return $this->hasOne('OfferField', 'content_id');
  }
  
}