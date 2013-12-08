<?php

class BlogController extends BaseController {

  public $layout = 'layout.master';

  public function index() {

    $this->layout->title = 'Blog';   	  
  	$this->layout->content = $this->renderBlogIndex();
  
   }
   
   public function renderBlogIndex() {
    
    $current_page = Paginator::getCurrentPage();
    
    return Cache::
     remember(
       'blog-index-' . $current_page , 
       1, 
       function() 
     {
       
    $items = array();
    
    $blogs = Blog::orderBy('created_at', 'desc')
      ->with('user', 'destinations', 'field')
      ->paginate(30);

    foreach($blogs as $blog) {
      $items[] = View::make('blog.item_' . (strlen($blog->body) < 1500 ? 'short' : 'long'))
        ->with('item', $blog);
 	  }
 	  
 	  return View::make('layout.table')
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

  	$content = $this->renderContentShow($id, 'Blog', 'content.show', 'comment.item_small'); 	
    $this->layout->title = $content->title;   	  
  	$this->layout->content = $content->content; 

   }

 }