<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertMisc extends ConvertBase {
	
	protected $name = 'nizza:convert-misc';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
	public function fire() {
    
    $this->createDestinations();
    $this->createTopics();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count +1 . ' set of miscs to queue');
      
        Queue::push('ConvertMisc@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	  	
 		$miscs_old = DB::connection('trip')
      ->table('node')
      ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
      ->select('*', 'node.uid')
      ->where('status', '=', 1)
      ->where('type', '=', 'trip_forum_other')
      ->orderBy('last_comment', 'desc')      
      ->skip($data['count'] * $data['batch'])
      ->take($data['batch'])
      ->get();

    foreach($miscs_old as $misc_old) {

      if (!Forum::find($misc_old->nid)) {

      $misc = new Forum;
      $misc->id = $misc_old->nid;
      $misc->forum_type = 'Misc';
      $misc->user_id = $misc_old->uid;
      $misc->title = $misc_old->title;
      $misc->body = $misc_old->body;
      
      
      $misc->created_at = Carbon::createFromTimeStamp($misc_old->created);  
      $misc->updated_at = Carbon::createFromTimeStamp($misc_old->last_comment);  

      $misc->save();  
      $this->createUser($misc_old->uid);
      $this->createComments($misc_old->nid, 'Forum');
      $this->attachDestinations($misc_old->nid, 'Forum');                     
    
  //    $this->attachFlags($misc_old->nid, 'Misc');
    
     }

   }

   $job->delete();

  }
 
        
}