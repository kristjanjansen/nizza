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
        
    $blogs_old = DB::connection('trip')
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

    foreach($blogs_old as $new_old) {

      $new = new News;
      $new->id = $new_old->nid;
      $new->type = get_class($new);
      $new->user_id = $new_old->uid;
      $new->title = $new_old->title;
      $new->body = $new_old->body;
      $new->created_at = Carbon::createFromTimeStamp($new_old->created);  
      $new->updated_at = Carbon::createFromTimeStamp($new_old->last_comment);  

      $fields = new NewsField;
      $fields->content_id = $new_old->nid;
      $fields->url = $new_old->field_lyhiuudislink_url ? $new_old->field_lyhiuudislink_url : '';

      // @TODO fetch image
      
      $pattern = "/.*\s*<!--\s*FRONTIMG:\s*(.*)\s*-->.*/";
      
      if (preg_match($pattern, $new->body, $matches)) {
        $fields->image = $matches[1];
        $new->body = trim(preg_replace($pattern, '', $new->body));
      }

      $new->save();
      $fields->save();
            

      $this->createUser($new_old->uid);
      $this->createComments($new_old->nid, 'News');
      $this->attachDestinations($new_old->nid);                     

    }
              
   $job->delete();

  }

        
}