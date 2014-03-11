<?php

class Offer extends Eloquent {

    protected $table = 'offer';

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
  
}