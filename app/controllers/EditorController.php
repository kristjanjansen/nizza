<?php

class EditorController extends BaseController {

    public $layout = 'layout.master';

    public function index() {

      $current_page = Paginator::getCurrentPage();

      $this->layout->title = 'Editor';   	  
    	$this->layout->content = Cache::
          remember(
              'editor-index-' . $current_page , 
              1, 
              function() {
                  return $this->renderIndex();
              }); 
     }
     


    public function renderIndex() {

        $items = array();

        $forums = Editor::orderBy('created_at', 'desc')
          ->with('user');

       
      $forums = $forums->paginate(30);

        foreach($forums as $forum) {
          $items[] = View::make('editor.item')
            ->with('item', $forum);
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

   	    $item = Editor::findOrFail($id);
         $item->load('user','comments','comments.user');

         $content = View::make('editor.show')
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