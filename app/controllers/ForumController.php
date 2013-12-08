<?php

class ForumController extends ContentController {

  public function index() {
          
     $this->layout->title = 'Forum';   	  
  	 $this->layout->content = $this
  	    ->renderContentIndex('Forum', 'forum.item', null, array('user', 'destinations', 'topics'));
  	$this->layout->content .= $this
      ->renderBlockDestination('Forum');
    $this->layout->content .= '<p />' . $this
      ->renderBlockTopic();  
     
   }

   public function show($id) {

   	$content = $this->renderContentShow($id, 'Forum'); 	
    $this->layout->title = $content->title;   	  
   	$this->layout->content = $content->content; 

    }
    

    public function renderContentIndex($type, $view_item = null, $view_layout = null, $with = array()) {

      $destination = Input::get('destination_id');
      $topic = Input::get('topic_id');
      $current_page = Paginator::getCurrentPage();
      
       return Cache::
        remember(
          'content-index-forum-' . $destination . '-' . $topic . '-' . $current_page , 
          1, 
          function() 
          use ($type, $view_item, $view_layout, $with, $destination, $topic) 
        {
          
        $output = array();

        $items = $type::
          select('*', 'content.id')
          ->with($with ? $with : array('user', 'destinations'))
          ->orderBy('updated_at', 'desc');

        if (Input::has('destination_id')) $items = $items->filterDestination();

        if (Input::has('topic_id')) $items = $items->filterTopic();      
          
        $items = $items->paginate(30);

        foreach($items as $item) {
          $output[] = View::make($view_item ? $view_item : 'content.item')
            ->with('item', $item)
            ->with('type', $type);
     	  }

     	  return View::make($view_layout ? $view_layout : 'layout.table')
          ->with('items', $output)
          ->with('pager', 
            Paginator::
              make(array(), PHP_INT_MAX, 30)
              ->appends('destination_id', $destination)
              ->appends('topic_id', $topic)
              ->links()
            )
          ->render();

      });
      
    }


  	public function renderBlockDestination($type) {

      $topic = Input::get('topic_id');
      
      return Cache::
        remember(
          'block-destination-' . $type . '-' . $topic, 
          1, 
          function() 
          use ($type, $topic)
        {
          
  	  $dests = Destination::has($type)->orderBy('title')->get();
      
      $output = HTML::linkAction($type . 'Controller@index', 'All', array('topic_id' => Input::get('topic_id')));

      foreach($dests as $dest) {

  	    $output .= ' ' . HTML::linkAction($type . 'Controller@index', $dest->title, array('destination_id' => $dest->id, 'topic_id' => $topic));

  	  }

  	  return $output;
  	
  	});
  	
  	}
  	       
    public function renderBlockTopic() {

      $destination = Input::get('destination_id');
      
      return Cache::
        remember(
          'block-topic-Forum-' . $destination, 
          1, 
          function() 
          use ($destination)
        {
          
  	  $topics = Topic::has('Forum')->orderBy('title')->get();

      $out = HTML::linkAction('ForumController@index', 'All', array('destination_id' => Input::get('destination_id')));

      foreach($topics as $topic) {
        
  	    $out .= ' ' . HTML::linkAction('ForumController@index', $topic->title, array('destination_id' => $destination, 'topic_id' => $topic->id));
  	      	  
  	  }

  	  return $out;
 
      });
  	
  	}
 
}