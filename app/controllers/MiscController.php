<?php

class MiscController extends ContentController {
    
    public function index() {

       $this->layout->title = 'Misc';   	  
    	  $this->layout->content = $this
    	    ->renderContentIndex('Misc'); 	  

     }

     public function show($id) {

     	$content = $this->renderContentShow($id, 'Misc'); 	
        $this->layout->title = $content->title;   	  
     	 $this->layout->content = $content->content; 

      }

 }