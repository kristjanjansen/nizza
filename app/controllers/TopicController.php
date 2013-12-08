<?php

class TopicController extends BaseController {

  public $layout = 'layout.master';

  public function show($id) {
    $topic = Topic::findOrFail($id);
    $topic->load('forums','forums.user', 'forums.comments','forums.comments.user', 'forums.destinations', 'forums.topics');
    
    if ($topic) {
 
      foreach($topic->forums as $forum) {
        $items[] = View::make('forum.item')
          ->with('forum', $forum);
   	  }
   	  
      $this->layout->title = $topic->title;
   	  $this->layout->content = View::make('layout.table')
        ->with('items', $items);
             
    }
  
  }

}
