<?php

class Blog extends Content {

  public function field() {
      return $this->hasOne('BlogField', 'content_id');
  }
  
}