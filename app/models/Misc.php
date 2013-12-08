<?php

class Misc extends Content {

  public function flags()
  {
      return $this->morphMany('Flag', 'flaggable');
  }
          
}