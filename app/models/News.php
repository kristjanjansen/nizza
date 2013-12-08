<?php

class News extends Content {

  public function field() {
      return $this->hasOne('NewsField', 'content_id');
  }
  

}