<?php

class ImageController extends ContentController {

    public function index() {

       $this->layout->title = 'Image';   	  
    	  $this->layout->content = $this
  	  ->renderContentIndex('Image', 'image.item', 'layout.grid', array('user', 'field'));
    	  $this->layout->content .= $this
          ->renderBlockDestination('Image');

     }

     public function show($id) {

     	$content = $this->renderContentShow($id, 'Image', 'image.show', 'comment.item_small'); 	
        $this->layout->title = $content->title;   	  
     	 $this->layout->content = $content->content; 

      }
      
 }