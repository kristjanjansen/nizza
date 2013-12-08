<?php

class Topic extends Eloquent {

  protected $table = 'topic';

  public $timestamps = false;
  
  public function forum() {
    return $this->belongsToMany('Forum', 'topic_map', 'topic_id', 'content_id');
  }

}