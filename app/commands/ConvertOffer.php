<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertOffer extends ConvertBase {
	
	protected $name = 'nizza:convert-offer';
  
	public function __construct(Filesystem $files) {
		parent::__construct($files);
	}
	
	public function fire() {
        
    $this->createDestinations();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count + 1 . ' set of offers to queue');
      
        Queue::push('ConvertOffer@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	
  	// @TODO Select
  	
		$offers_old = DB::connection('trip')
     ->table('node')
     ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
     ->join('content_type_trip_offer', 'content_type_trip_offer.nid', '=', 'node.nid')
     ->select('*', 'node.uid')     
     ->where('node.status', '=', 1) // ?
     ->where('node.type', '=', 'trip_offer')
     ->orderBy('node.last_comment', 'desc')      
     ->skip($data['count'] * $data['batch'])
     ->take($data['batch'])
     ->get();

   foreach($offers_old as $offer_old) {

     $offer = new Offer;
     $offer->id = $offer_old->nid;
     $offer->type = get_class($offer);
     $offer->user_id = $offer_old->uid;
     $offer->title = $offer_old->title;
     $offer->body = $offer_old->body;
     
     /*
     
     content_type_trip_offer:     
     field_date_duration_value
     field_date_end_value (timestamp)
     field_date_expire_comp_value (computed)
     field_description_value
     field_link_url
     field_price_value
     field_price_display_value (comp?)
     field_price_flights_value
     field_start_location_value
      1|Eestist
      2|Lätist
      3|Soomest
      4|Rootsist
      100|Mujalt
     field_text_additional_value
     field_text_extras_value    
     field_text_included_value
     field_travel_type_value
      1|Nädalalõpureis
      2|Eksootikareis
      3|Kruiis
      4|Ringreis
      5|Omal käel reis
     field_date_start_value
     field_date_expire_value
     field_date_publish_value     
     field_title_value
     field_text_itinerary_value
     field_date_start_comp_value (comp ?)     
     field_onhold_value (?)
     field_includeinadvertising_value
     field_advertisinglimit_value
     
     content_field_hotels:
     field_hotels_nid
     delta
     
     content_field_prices:
     field_prices_value
     delta
    
     new Hotel;
     
     node.type = trip_hotel
     content_type_trip_hotel
     field_rating_value
     
     */ 
     
     $offer->created_at = Carbon::createFromTimeStamp($offer_old->created);  
     $offer->updated_at = Carbon::createFromTimeStamp($offer_old->last_comment);  

     $offer->save();
     
     $fields = new OfferField;
     $fields->content_id = $offer_old->nid;
     $fields->url = $offer_old->field_link_url ? $offer_old->field_link_url : '';
     $fields->save();
     
     $this->createUser($offer_old->uid); // type!
     $this->createComments($offer_old->nid, 'Offer'); // ?
     $this->attachDestinations($offer_old->nid, 'Offer');                     
       
   }

   $job->delete();

  }
 
        
}