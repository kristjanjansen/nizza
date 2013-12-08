<?php

class FlightController extends ContentController {

  public $layout = 'layout.master';

  public function index() {
          
     $this->layout->title = 'Flight';   	  
  	  $this->layout->content = $this
  	    ->renderContentIndex('Flight', 'flight.item', null, array('field', 'field.carrier'));
  	  $this->layout->content .= $this
      	    ->renderBlockDestination('Flight');	  
     
   }
           
      	public function show($id, $type = null, $view_show = null, $view_comment = null) {
  	  
  	  $flight = Flight::findOrFail($id);
      $flight->load('user','comments','comments.user', 'destinations', 'field', 'field.carrier');


  	    $this->layout->title = $flight->title;
     	  $this->layout->content = View::make('flight.show')
          ->with('item', $flight);

        $items = array();
                
        foreach($flight->comments->sortBy(function($comments) {return $comments->created_at;}, 'desc') as $comment) {
          $items[] = View::make('comment.item_small')
            ->with('comment', $comment);
     	  }
     	  
     	  $this->layout->content .= View::make('layout.table')
          ->with('items', $items);
    
    }


 }