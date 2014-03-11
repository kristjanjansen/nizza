<?php

class News extends Eloquent {

    protected $table = 'news';

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