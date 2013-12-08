<?php

class UserController extends BaseController {

  public $layout = 'layout.master';

  public function show($id) {
    
    // @TODO: Add ->with() when displaying more fields
    
    $user = User::findOrFail($id);
    
    $this->layout->title = $user->name;
    $this->layout->content = View::make('user.show')
      ->with('user', $user);
  
  }
      
 }