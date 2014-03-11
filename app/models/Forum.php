<?php

class Forum extends Eloquent {
 
    protected $table = 'forum';

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

    public function topics()
    {
        return $this->morphToMany('Topic', 'topicable');
    }
    
    public function flags() {
        return $this->morphMany('Flag', 'flaggable');
    }
        
/*
   public function scopeDestination($query, $destination_id) {
       return $query
         ->join(
           'topic_map', 'content.id', '=', 'topic_map.content_id'
         )
         ->where('topic_map.topic_id', '=', Input::get('topic_id'));
   }
   */
       
}