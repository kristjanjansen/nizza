<?php

class Content extends Eloquent {

  protected $table = 'content';

  public function newQuery($excludeDeleted = true)
  {
      $query = parent::newQuery($excludeDeleted);
      $query->where('type', '=', get_class($this));
      return $query;
  }
  
    
  public function user() {
      return $this->belongsTo('User');
  }

  public function comments() {
      return $this->hasMany('Comment', 'content_id');
  }

  public function destinations() {
    return $this->belongsToMany('Destination','destination_map', 'content_id');
  }
  
  public function scopeFilterDestination($query) {
    return $query
        ->join(
          'destination_map', 'content.id', '=', 'destination_map.content_id'
        )
        ->where('destination_map.destination_id', '=', Input::get('destination_id'));
  }

}