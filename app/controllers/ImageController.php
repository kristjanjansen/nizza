<?php

class ImageController extends BaseController {

    public $layout = 'layout.master';

    public function index() {

      $current_page = Paginator::getCurrentPage();

      $this->layout->title = 'Image';   	  
    	$this->layout->content = Cache::
          remember(
              'image-index-' . $current_page , 
              1, 
              function() {
                  return $this->renderIndex();
              }); 
     }

     public function renderIndex() {


      $items = array();

      $images = Image::orderBy('created_at', 'desc')
        ->with('user', 'destinations')
        ->paginate(30);

      foreach($images as $image) {
        $items[] = View::make('image.item')
          ->with('item', $image);
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

    	    $item = Image::findOrFail($id);
          $item->load('user','comments','comments.user', 'destinations');

          $content = View::make('image.show')
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