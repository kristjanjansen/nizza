<?php

class TravelmateController extends ContentController {

  public function index() {
          
     $this->layout->title = 'Travelmate';   	  
  	  $this->layout->content = $this
  	    ->renderContentIndex('Travelmate', 'travelmate.item');
  	  $this->layout->content .= $this
        ->renderBlockDestination('Travelmate');
     
   }

   public function show($id) {

   	$content = $this->renderContentShow($id, 'Travelmate', 'travelmate.show', 'comment.item_small'); 	
      $this->layout->title = $content->title;   	  
   	 $this->layout->content = $content->content; 

    }

 }