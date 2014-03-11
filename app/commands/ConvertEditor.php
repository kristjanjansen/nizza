<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertEditor extends ConvertBase {
	
	protected $name = 'nizza:convert-editor';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
	public function fire() {
    
    $this->createDestinations();
    $this->createTopics();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count +1 . ' set of editors to queue');
      
        Queue::push('ConvertEditor@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	  	
 		$editors_old = DB::connection('trip')
      ->table('node')
      ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
      ->select('*', 'node.uid')      
      ->where('status', '=', 1)
      ->where('type', '=', 'trip_forum_editor')
      ->orderBy('last_comment', 'desc')      
      ->skip($data['count'] * $data['batch'])
      ->take($data['batch'])
      ->get();

    foreach($editors_old as $editor_old) {

      if (!Editor::find($editor_old->nid)) {

      $editor = new Editor;
      $editor->id = $editor_old->nid;
      $editor->user_id = $editor_old->uid;
      $editor->title = $editor_old->title;
      $editor->body = $editor_old->body;

      $editor->created_at = Carbon::createFromTimeStamp($editor_old->created);  
      $editor->updated_at = Carbon::createFromTimeStamp($editor_old->last_comment);  
      
      $editor->save();  
      $this->createUser($editor_old->uid);
      $this->createComments($editor_old->nid, 'Editor');
    
     }

   }

   $job->delete();

  }
 
        
}