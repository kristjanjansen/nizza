<?php

class Expat extends Content {
 
  public function flags()
  {
      return $this->morphMany('Flag', 'flaggable');
  }

  
}