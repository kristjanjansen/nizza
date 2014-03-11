<?php

class ForumController extends BaseController {

    public $layout = 'layout.master';

    public function index() {

      $forum_type = Input::get('forum_type');
      $destination_id = Input::get('destination_id');
      $topic_id = Input::get('topic_id');
      $current_page = Paginator::getCurrentPage();

      $this->layout->title = 'Forum';   	  
    	$this->layout->content = Cache::
          remember(
              'forum-index-' . $forum_type . '-' . $destination_id . '-' . $topic_id . '-' . $current_page , 
              1, 
              function() use ($forum_type, $destination_id, $topic_id) {
                  return $this->renderIndex($forum_type, $destination_id, $topic_id);
              }); 
     }
     


    public function renderIndex($forum_type = null, $destination_id = null, $topic_id = null) {

        $items = array();

        $forums = Forum::orderBy('created_at', 'desc')
          ->with('user', 'destinations', 'topics', 'flags');

      if ($forum_type) {
            $forums = $forums->where('forum_type', '=', $forum_type);
      }
        
    
      if ($destination_id) {
          $forums = $forums
           ->join('destinationables', 'forum.id', '=', 'destinationables.destinationable_id')
           ->where('destinationables.destinationable_type', '=', 'Forum')
           ->where('destinationables.destination_id', '=', $destination_id);         
      }
      

      if ($topic_id) {
          $forums = $forums
           ->join('topicables', 'forum.id', '=', 'topicables.topicable_id')
           ->where('topicables.topicable_type', '=', 'Forum')
           ->where('topicables.topic_id', '=', $topic_id);         
      }


           
/*        if ($destination_id) {
            $forums = $forums->whereHas('destinations', function($q) use ($destination_id) {
                $q->where('id', '=', $destination_id);
            });
        }

      if (Input::has('topic_id')) {
          $forums = $forums->whereHas('topics', function($q) {
              $q->where('id', '=', Input::get('topic_id'));
          });
      }
        */
             
        
      $forums = $forums->paginate(30);

        foreach($forums as $forum) {
          $items[] = View::make('forum.item')
            ->with('item', $forum);
     	  }

     	  return View::make('layout.table')
          ->with('items', $items)
          ->with('pager', 
            Paginator::
              make(array(), PHP_INT_MAX, 30)
              ->appends('destination_id', $destination_id)
              ->appends('topic_id', $topic_id)              
              ->links()
            )
          ->render();
               
    }


     public function show($id) {

         $content = '';
         $items = array();

   	    $item = Forum::findOrFail($id);
         $item->load('user','comments','comments.user', 'destinations', 'topics', 'flags', 'comments.flags');

         $content = View::make('forum.show')
             ->with('item', $item);

         foreach($item->comments as $comment) {
             $items[] = View::make('comment.item')
                 ->with('comment', $comment);
      	}

      	$content .= View::make('layout.table')
             ->with('items', $items)
             ->render();

         $this->layout->title = $item->title;   	  
        	$this->layout->content = $content;

    }

 
}