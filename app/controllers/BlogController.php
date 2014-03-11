<?php

class BlogController extends BaseController {

  public $layout = 'layout.master';

  public function index() {

    $current_page = Paginator::getCurrentPage();

    $this->layout->title = 'Blog';   	  
  	$this->layout->content = Cache::
        remember(
            'blog-index-' . $current_page , 
            1, 
            function() {
                return $this->renderBlogIndex();
            }); 
   }
   
   public function renderBlogIndex() {
    
       
    $items = array();
    
    $blogs = Blog::orderBy('created_at', 'desc')
      ->with('user', 'destinations')
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
      
  }
  
  
    public function show($id) {
      
        $content = '';
        $items = array();

  	    $item = Blog::findOrFail($id);
        $item->load('user','comments','comments.user', 'destinations');
        
        $content = View::make('blog.show')
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