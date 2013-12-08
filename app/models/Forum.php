<?php

class Forum extends Content {
 
  public function topics() {
     return $this->belongsToMany('Topic','topic_map', 'content_id');
   }

   public function scopeFilterTopic($query) {
       return $query
         ->join(
           'topic_map', 'content.id', '=', 'topic_map.content_id'
         )
         ->where('topic_map.topic_id', '=', Input::get('topic_id'));
   }

   public function flags()
   {
       return $this->morphMany('Flag', 'flaggable');
   }
     
}