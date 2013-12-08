<?php

class DestinationController extends BaseController {

  public $layout = 'layout.master';

  public function show($id) {
    $destination = Destination::findOrFail($id);
    $destination->load('forums','forums.user', 'forums.comments','forums.comments.user', 'forums.destinations', 'forums.topics');
    
    if ($destination) {
      
      foreach($destination->forums as $forum) {
        $items[] = View::make('forum.item')
          ->with('forum', $forum);
   	  }
   	  
      $this->layout->title = $destination->title;
   	  $this->layout->content = View::make('layout.table')
        ->with('items', $items);
             
    }
  
  }

}
