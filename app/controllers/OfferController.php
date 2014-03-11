<?php

class OfferController extends BaseController {

    public $layout = 'layout.master';
    
    public function index() {

      $current_page = Paginator::getCurrentPage();

      $this->layout->title = 'Offer';   	  
    	$this->layout->content = Cache::
          remember(
              'offer-index-' . $current_page , 
              1, 
              function() {
                  return $this->renderIndex();
              }); 
     }

     public function renderIndex() {


      $items = array();

      $offers = Offer::orderBy('created_at', 'desc')
        ->with('user', 'destinations')
        ->paginate(30);

      foreach($offers as $offer) {
        $items[] = View::make('offer.item')
          ->with('item', $offer);
   	  }

   	  return View::make('layout.grid')
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

    	    $item = Offer::findOrFail($id);
          $item->load('user','comments','comments.user', 'destinations');

          $content = View::make('offer.show')
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