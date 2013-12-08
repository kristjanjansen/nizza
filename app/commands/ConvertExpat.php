<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertExpat extends ConvertBase {
	
	protected $name = 'nizza:convert-expat';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
	public function fire() {
    
    $this->createDestinations();
    $this->createTopics();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count +1 . ' set of expats to queue');
      
        Queue::push('ConvertExpat@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	  	
 		$expats_old = DB::connection('trip')
      ->table('node')
      ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
      ->select('*', 'node.uid')      
      ->where('status', '=', 1)
      ->where('type', '=', 'trip_forum_expat')
      ->orderBy('last_comment', 'desc')      
      ->skip($data['count'] * $data['batch'])
      ->take($data['batch'])
      ->get();

    foreach($expats_old as $expat_old) {

      if (!Expat::find($expat_old->nid)) {

      $expat = new Expat;
      $expat->id = $expat_old->nid;
      $expat->type = get_class($expat);      
      $expat->user_id =  $expat_old->uid; 
      $expat->title = $expat_old->title;
      $expat->body = $expat_old->body;

      $expat->created_at = Carbon::createFromTimeStamp($expat_old->created);  
      $expat->updated_at = Carbon::createFromTimeStamp($expat_old->last_comment);  
      
      $expat->save();  
      
      $this->createUser($expat_old->uid);
      $this->createComments($expat_old->nid, 'Expat');
      $this->attachDestinations($expat_old->nid);                     
    
      $this->attachFlags($expat_old->nid, 'Expat');
    
     }

   }

   $job->delete();

  }
 
        
}