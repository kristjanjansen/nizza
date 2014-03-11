<?php

class FlightController extends BaseController {

  public $layout = 'layout.master';

  public function index() {

    $current_page = Paginator::getCurrentPage();

    $this->layout->title = 'Flight';   	  
  	$this->layout->content = Cache::
        remember(
            'flight-index-' . $current_page , 
            1, 
            function() {
                return $this->renderIndex();
            }); 
   }
   
   public function renderIndex() {
    
       
    $items = array();
    
    $flights = Flight::orderBy('created_at', 'desc')
      ->with('user', 'destinations', 'carrier')
      ->paginate(30);

    foreach($flights as $flight) {
      $items[] = View::make('flight.item')
        ->with('item', $flight);
 	  }
 	  
 	  return View::make('layout.table')
      ->with('items', $items)
      ->with('pager', 
        Paginator::
          make(array(), PHP_INT_MAX, 30)
          ->links()
        )
      ->render();
      
  }
           
   public function show($id) {
     
       $content = '';
       $items = array();

 	   $item = Flight::findOrFail($id);
       $item->load('user','comments','comments.user', 'destinations', 'carrier');
       
       $content = View::make('flight.show')
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