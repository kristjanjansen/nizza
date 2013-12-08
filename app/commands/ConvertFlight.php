<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertFlight extends ConvertBase {
	
	protected $name = 'nizza:convert-flight';
  
	public function __construct(Filesystem $files) {
		parent::__construct($files);
	}
	
	public function fire() {
        
    $this->createDestinations();
    $this->createCarriers();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count +1 . ' set of flights to queue');
      
        Queue::push('ConvertFlight@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	
  	// From flights
  	
		$flights_old = DB::connection('trip')
     ->table('node')
     ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
     ->join('content_type_lennufirmade_sooduspakkumine', 'content_type_lennufirmade_sooduspakkumine.nid', '=', 'node.nid')
     ->join('term_node', 'term_node.nid', '=', 'node.nid')                   
     ->where('node.status', '=', 1)
     ->where('node.type', '=', 'lennufirmade_sooduspakkumine')
     ->orderBy('node.created', 'desc')
     ->skip($data['count'] * $data['batch'])
     ->take($data['batch'])
     ->get();

   foreach($flights_old as $flight_old) {

     $flight = new Flight;
     $flight->id = $flight_old->nid;
     $flight->type = get_class($flight);
     $flight->user_id = $flight_old->uid;
     $flight->title = $flight_old->title;
     
     $flight->body = $flight_old->body;

     $flight->body .= $flight_old->field_tripeecomment_value ? "\n\n" . $flight_old->field_tripeecomment_value : '' ; 
       
     /*
     content_type_lennufirmade_sooduspakkumine:
     field_salesperiod_value (datetime 2012-06-30T00:00:00)
     field_salesperiod_value2 (datetime)         
     field_originatingcities_value
    
     content_field_flightperiod:
     field_flightperiod_value (datetime)
     field_flightperiod_value2 (datetime)
     delta
    
     */
     
     $flight->created_at = Carbon::createFromTimeStamp($flight_old->created);  
     $flight->updated_at = Carbon::createFromTimeStamp($flight_old->last_comment);  

     $flight->save();
     
     $fields = new FlightField;
     $fields->content_id = $flight_old->nid;
     $fields->url = $flight_old->field_linktooffer_url ? $flight_old->field_linktooffer_url : '';
     $fields->carrier_id = $flight_old->tid;
     $fields->save();
     
     $this->createUser($flight_old->uid);
     $this->createComments($flight_old->nid, 'Flight');
     $this->attachDestinations($flight_old->nid);                     
             
   }
   
    // From forum
    
		$flights_old = DB::connection('trip')
     ->table('node')
     ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
     ->join('term_node', 'term_node.nid', '=', 'node.nid')
     ->select('*', 'node.uid')     
     ->where('node.status', '=', 1)
     ->where('node.type', '=', 'trip_forum')
     ->where('term_node.tid', '=', 825)
     ->orderBy('node.created', 'desc')
     ->skip($data['count'] * $data['batch'])
     ->take($data['batch'])                   
     ->get();

   foreach($flights_old as $flight_old) {

     $flight = new Flight;
     $flight->id = $flight_old->nid;
     $flight->type = get_class($flight);
     $flight->user_id = $flight_old->uid;
     $flight->title = '(from forum) ' . $flight_old->title;
     $flight->body = $flight_old->body;
     $flight->created_at = Carbon::createFromTimeStamp($flight_old->created);  
     $flight->updated_at = Carbon::createFromTimeStamp($flight_old->last_comment);  

     $flight->save();

     $this->createUser($flight_old->uid);
     $this->createComments($flight_old->nid);
     $this->attachDestinations($flight_old->nid);                     

   }
   
   
   $job->delete();

  }
 
        
}