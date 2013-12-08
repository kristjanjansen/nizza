<?php

class ContentController extends BaseController {

    public $layout = 'layout.master';

    public function index() {
      
    }

    public function show($id) {
      
    }

    public function renderContentIndex($type, $view_item = null, $view_layout = null, $with = array()) {

      $destination = Input::get('destination_id');
      $current_page = Paginator::getCurrentPage();
      
       return Cache::
        remember(
          'content-index-' . $type . '-' . $destination . '-' . $current_page , 
          1, 
          function() 
          use ($type, $view_item, $view_layout, $with, $destination) 
        { 
             
        $output = array();

        $items = $type::
          select('*', 'content.id')
          ->with($with ? $with : array('user', 'destinations'))
          ->orderBy('updated_at', 'desc');

        if ($destination) $items = $items->filterDestination();

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
              ->links()
            )
          ->render();
      
        });

      }


    	public function renderContentShow($id, $type, $view_show = null, $view_comment = null) {
        
        
        return Cache::
          remember(
            'content-show-' . $id , 
            1, 
            function() 
            use ($id, $type, $view_show, $view_comment) 
          {
            
        $content = new stdClass;
        
    	  $item = $type::findOrFail($id);
        $item->load('user','comments','comments.user', 'destinations', 'comments.flags');

        $content->title = $item->title;
        
       	  $content->content = View::make($view_show ? $view_show : 'content.show')
            ->with('item', $item);

          $items = array();

          foreach($item->comments as $comment) {
            $items[] = View::make($view_comment ? $view_comment : 'comment.item')
              ->with('comment', $comment);
       	  }

       	  $content->content .= View::make('layout.table')
            ->with('items', $items)
            ->render();
            
          return $content;
            
            
          });
        }

    	
    	public function renderBlockDestination($type) {

        return Cache::
          remember(
            'block-destination-' . $type , 
            1, 
            function() 
            use ($type)
          {
            
    	  $dests = Destination::has($type)->orderBy('title')->get();
        
        $output = HTML::linkAction($type . 'Controller@index', 'All');

        foreach($dests as $dest) {

    	    $output .= ' ' . HTML::linkAction($type . 'Controller@index', $dest->title, array('destination_id' => $dest->id));

    	  }

    	  return $output;
    	
    	});
    	
    	}

 }