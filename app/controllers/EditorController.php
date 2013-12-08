<?php

class EditorController extends ContentController {
    
  public function index() {
          
     $this->layout->title = 'Editor';   	  
  	  $this->layout->content = $this
  	    ->renderContentIndex('Editor'); 	  
     
   }

   public function show($id) {

   	$content = $this->renderContentShow($id, 'Editor'); 	
      $this->layout->title = $content->title;   	  
   	 $this->layout->content = $content->content; 

    }
    
 }