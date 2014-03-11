<?php

class NewsController extends BaseController {

  public $layout = 'layout.master';

  public function index() {

    $current_page = Paginator::getCurrentPage();

    $this->layout->title = 'News';   	  
  	$this->layout->content = Cache::
        remember(
            'news-index-' . $current_page , 
            1, 
            function() {
                return $this->renderIndex();
            }); 
   }
   
   public function renderIndex() {
          
    $items = array();
    
    $news = News::orderBy('created_at', 'desc')
      ->with('user', 'destinations')
      ->paginate(30);

    foreach($news as $new) {
      $items[] = View::make('news.item_' . (($new->url) ? 'short' : 'long'))
        ->with('item', $new);
 	  }
 	  
 	  return View::make('layout.list')
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

 	   $item = News::findOrFail($id);
       $item->load('user','comments','comments.user', 'destinations');
       
       $content = View::make('news.show')
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