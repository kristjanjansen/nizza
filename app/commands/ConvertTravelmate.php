<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertTravelmate extends ConvertBase {
	
	protected $name = 'nizza:convert-travelmate';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
	public function fire() {
    
    $this->createDestinations();
    $this->createTopics();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count + 1 . ' set of travelmates to queue');
      
        Queue::push('ConvertTravelmate@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	  	
 		$mates_old = DB::connection('trip')
      ->table('node')
      ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
      ->select('*', 'node.uid')
      ->where('status', '=', 1)
      ->where('type', '=', 'trip_forum_travelmate')
      ->orderBy('last_comment', 'desc')      
      ->skip($data['count'] * $data['batch'])
      ->take($data['batch'])
      ->get();

    foreach($mates_old as $mate_old) {

      if (!Travelmate::find($mate_old->nid)) {

      $travelmate = new Travelmate;
      $travelmate->id = $mate_old->nid;
      $travelmate->type = get_class($travelmate);     
      $travelmate->user_id = $mate_old->uid;
      $travelmate->title = $mate_old->title;
      $travelmate->body = $mate_old->body;
            
      $travelmate->created_at = Carbon::createFromTimeStamp($mate_old->created);  
      $travelmate->updated_at = Carbon::createFromTimeStamp($mate_old->last_comment);  
      
      /*
      content_field_reisitoimumine:
      field_reisitoimumine_value (timestamp)
      
      content_field_reisikestvus:
      field_reisikestvus_value
            
      content_field_millistkaaslastsoovidleida:      
      field_millistkaaslastsoovidleida
      |
      Kõik sobib
      Mees
      Naine
      (+ ignore other values)
      
      Reisistiil vid 5 
      
      */
      
      $travelmate->save();  
      $this->createUser($mate_old->uid);
      $this->createComments($mate_old->nid, 'Travelmate');
      $this->attachDestinations($mate_old->nid);                     
    
     }

   }

   $job->delete();

  }
 
        
}