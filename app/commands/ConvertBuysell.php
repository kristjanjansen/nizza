<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertBuysell extends ConvertBase {
	
	protected $name = 'nizza:convert-buysell';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
	public function fire() {
    
    $this->createDestinations();
    $this->createTopics();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count + 1 . ' set of buysells to queue');
      
        Queue::push('ConvertBuysell@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	  	
 		$buysells_old = DB::connection('trip')
      ->table('node')
      ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
      ->select('*', 'node.uid')      
      ->where('status', '=', 1)
      ->where('type', '=', 'trip_forum_buysell')
      ->orderBy('last_comment', 'desc')      
      ->skip($data['count'] * $data['batch'])
      ->take($data['batch'])
      ->get();

    foreach($buysells_old as $buysell_old) {

      if (!Buysell::find($buysell_old->nid)) {

      $forum = new Buysell;
      $forum->id = $buysell_old->nid;
      $forum->type = get_class($forum);     
      $forum->user_id = $buysell_old->uid;
      $forum->title = $buysell_old->title;
      $forum->body = $buysell_old->body;

      $forum->created_at = Carbon::createFromTimeStamp($buysell_old->created);  
      $forum->updated_at = Carbon::createFromTimeStamp($buysell_old->last_comment);  
      
      
      /*
      
      Kategooria vid 25
       Matkavarustus 4408
       Reisikirjandus 4407
       Reisöömaja 4409
       Voucherid ja piletid 4406
       
      Kuulutuse tyyp vid 22
       Müün 4311
       Ostan 4310
       Annan üürile 4410
       Võtan üürile 4411
       Annan ära 4412
       Muu 4312
      
      content_type_trip_forum_buysell:      
      field_buysellprice_value
      field_buysellcontact_value      
 
     */
      
      $forum->save();  
      $this->createUser($buysell_old->uid);
      $this->createComments($buysell_old->nid, 'Buysell');
      $this->attachDestinations($buysell_old->nid);                     
    
     }

   }

   $job->delete();

  }
 
        
}