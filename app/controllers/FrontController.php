<?php

class FrontController extends BaseController {

  public $layout = 'layout.master';

  public function index() {

 	  $this->layout->title = '';

    // News
    
    $news = News::orderBy('created_at', 'desc')
      ->take(2)
      ->get();
      
    $items = array();
    
    foreach($news as $new) {
      $items[] = View::make('news.item_short')
        ->with('item', $new);
    }

    $this->layout->content = View::make('layout.list')
      ->with('items', $items);  

    // Images
    
    $images = Image::orderBy('created_at', 'desc')
      ->take(4)
      ->with('field')
      ->get();
    
    $items = array();
    
    foreach($images as $image) {
      $items[] = View::make('image.item')
        ->with('item', $image);
    }
    
    $this->layout->content .= View::make('layout.grid')
      ->with('items', $items);


    // Forum
    
    $items = array();
          
    $forums = Forum::orderBy('created_at', 'desc')
      ->take(30)
      ->with('user', 'destinations', 'topics')
      ->get();
      
    foreach($forums as $forum) {
      $items[] = View::make('forum.item')
        ->with('item', $forum);
 	  }
 	  
 	  $this->layout->content .= View::make('layout.table')
      ->with('items', $items);


  
  }

 }