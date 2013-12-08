<?php

class Image extends Content {

  public function field() {
      return $this->hasOne('ImageField', 'content_id');
  }

  
}