<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertNews extends ConvertBase {
	
	protected $name = 'nizza:convert-news';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
	public function fire() {
    
    $this->createDestinations();
		
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count + 1 . ' set of news to queue');
      
        Queue::push('ConvertNews@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }
		  
	}
	
  public function convert($job, $data) {
        
    $news_old = DB::connection('trip')
      ->table('node')
      ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
      ->join('content_type_story', 'content_type_story.nid', '=', 'node.nid')
      ->select('*', 'node.uid')
      ->where('node.status', '=', 1)
      ->where('node.type', '=', 'story')
      ->orderBy('node.created', 'desc')
      ->skip($data['count'] * $data['batch'])
      ->take($data['batch'])         
      ->get();

    foreach($news_old as $new_old) {

      $new = new News;
      $new->id = $new_old->nid;
      $new->user_id = $new_old->uid;
      $new->title = $new_old->title;
      $new->body = $new_old->body;
      $new->created_at = Carbon::createFromTimeStamp($new_old->created);  
      $new->updated_at = Carbon::createFromTimeStamp($new_old->last_comment);  

      $new->url = $new_old->field_lyhiuudislink_url ? $new_old->field_lyhiuudislink_url : '';

      // @TODO fetch image
      
      $pattern = "/.*\s*<!--\s*FRONTIMG:\s*(.*)\s*-->.*/";
      
      if (preg_match($pattern, $new->body, $matches)) {
        $new->image = $matches[1];
        $new->body = trim(preg_replace($pattern, '', $new->body));
      }

      $new->save();
            

      $this->createUser($new_old->uid);
      $this->createComments($new_old->nid, 'News');
      $this->attachDestinations($new_old->nid, 'News');                     

    }
              
   $job->delete();

  }

        
}