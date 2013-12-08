<?php

class Comment extends Eloquent {

  protected $table = 'comment';

  public function commentable()
  {
      return $this->belongsTo('Content');
  }
    
  public function user()
  {
      return $this->belongsTo('User');
  }

  public function flags()
  {
      return $this->morphMany('Flag', 'flaggable');
  }



}