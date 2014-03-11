<?php

class Travelmate extends Eloquent {
 
    protected $table = 'travelmate';

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