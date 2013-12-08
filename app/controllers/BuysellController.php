<?php

class BuysellController extends ContentController {
    
   public function index() {
           
      $this->layout->title = 'Buysell';   	  
   	  $this->layout->content = $this
   	    ->renderContentIndex('Buysell'); 	  
      
    }

    public function show($id) {

    	$content = $this->renderContentShow($id, 'Buysell', null, 'comment.item_small'); 	
       $this->layout->title = $content->title;   	  
    	 $this->layout->content = $content->content; 

     }

 }