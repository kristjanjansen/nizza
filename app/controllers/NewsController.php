<?php

class NewsController extends BaseController {

  public $layout = 'layout.master';

  public function index() {

    $this->layout->title = 'News';   	  
  	$this->layout->content = $this->renderNewsIndex();
  
   }
   
   public function renderNewsIndex() {
    
     $current_page = Paginator::getCurrentPage();
    
     return Cache::
      remember(
        'news-index-' . $current_page , 
        1, 
        function() 
      {
        
    $news = News::with('field')->orderBy('created_at', 'desc')
      ->paginate(30);
 	  
    $items = array();
    
    foreach($news as $new) {
      $items[] = View::make('news.item_' . (($new->field->url) ? 'short' : 'long'))
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

    });
    
  }


     public function show($id) {

     	$content = $this->renderContentShow($id, 'News', 'news.show', 'comment.item_small'); 	
      $this->layout->title = $content->title;   	  
     	$this->layout->content = $content->content; 

      }
	
 }