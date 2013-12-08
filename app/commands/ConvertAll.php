<?php

use Illuminate\Console\Command;

class ConvertAll extends ConvertBase {
	
	protected $name = 'nizza:convert-all';
	
	public function fire() {
    $this->call('nizza:convert-news');
    $this->call('nizza:convert-blog');    
    $this->call('nizza:convert-forum');    
    $this->call('nizza:convert-buysell');    
    $this->call('nizza:convert-travelmate');    
    $this->call('nizza:convert-expat');    
    $this->call('nizza:convert-misc');    
    $this->call('nizza:convert-image');    
    $this->call('nizza:convert-flight');    
    $this->call('nizza:convert-offer');    
  }
         
}