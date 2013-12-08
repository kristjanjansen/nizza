<?php

class ExpatController extends ContentController {
    
  public function index() {
          
     $this->layout->title = 'Expat';   	  
  	  $this->layout->content = $this
  	    ->renderContentIndex('Expat'); 	  
   	  $this->layout->content .= $this
    	    ->renderBlockDestination('Expat'); 	  
     
   }

   public function show($id) {

   	$content = $this->renderContentShow($id, 'Expat'); 	
      $this->layout->title = $content->title;   	  
   	 $this->layout->content = $content->content; 

    }

 }