<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertImage extends ConvertBase {
	
	protected $name = 'nizza:convert-image';
  
	public function __construct(Filesystem $files) {
		parent::__construct($files);
	}
	
	public function fire() {
    

    $this->createDestinations();
    $this->createTopics();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count +1 . ' set of images to queue');
      
        Queue::push('ConvertImage@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	  	
		$images_old = DB::connection('trip')
     ->table('node')
     ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
     ->join('content_field_image', 'content_field_image.nid', '=', 'node.nid')
     ->join('files', 'files.fid', '=', 'content_field_image.field_image_fid')
     ->select('*', 'node.uid')   
     ->where('node.status', '=', 1)
     ->where('node.type', '=', 'trip_image')
     ->orderBy('node.last_comment', 'desc')      
     ->skip($data['count'] * $data['batch'])
     ->take($data['batch'])
     ->get();

   foreach($images_old as $image_old) {

     $image = new Image;
     $image->id = $image_old->nid;
     $image->user_id = $image_old->uid;
     $image->title = $image_old->title;
     $image->body = $image_old->body;
     $image->created_at = Carbon::createFromTimeStamp($image_old->created);  
     $image->updated_at = Carbon::createFromTimeStamp($image_old->last_comment);  
     
     // @todo What's this?
     // Type vid 20
     // 4367  
     // 646 
     
     $image_path = basename($image_old->filename);
     
     // @todo Guzzle
     
     if (substr($this->image_src, 0, 4) == 'http') {
       $ch = curl_init($this->image_src . $image_path);
      curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_exec($ch);
      $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $exists = ($retcode == 200) ? true : false;
     } else {
      $exists = file_exists($this->image_src . $image_path) ? true : false;
     }
     
     if ($exists) {
       
       $this->files->copy(
          $this->image_src . $image_path,
          $this->image_tgt . $image_path,
        true);

     }
     
     // @TODO curl_close($ch)

     $image->filename = $image_old->filename; // @TODO basename()
     
     $image->save();
     
     
     $this->createUser($image_old->uid);
     $this->createComments($image_old->nid, 'Image');
     $this->attachDestinations($image_old->nid, 'Image');                     
       
   }

   $job->delete();

  }
 
        
}