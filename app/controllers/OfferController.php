<?php

class OfferController extends ContentController {

  public function index() {
          
    $this->layout->title = 'Offer';   	  
  	$this->layout->content = $this
  	  ->renderContentIndex('Offer', 'offer.item', 'layout.grid', array('user', 'field'));
  	$this->layout->content .= $this
      ->renderBlockDestination('Offer');  
     
   }

   public function show($id) {

     $content = $this->renderContentShow($id, 'Offer', 'offer.show', 'comment.item_small'); 	
     $this->layout->title = $content->title;   	  
   	 $this->layout->content = $content->content; 

  }
    
}