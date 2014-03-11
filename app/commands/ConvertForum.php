<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertForum extends ConvertBase {
	
	protected $name = 'nizza:convert-forum';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
  
  
	public function fire() {
    
    $this->createDestinations();
    $this->createTopics();
    	  
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count + 1 . ' set of forums to queue');
      
        Queue::push('ConvertForum@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }

		  
	}


  public function convert($job, $data) {
  	  	
 		$forums_old = DB::connection('trip')
      ->table('node')
      ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
      ->select('*', 'node.uid')
      ->where('status', '=', 1)
      ->where('type', '=', 'trip_forum')
      ->orderBy('last_comment', 'desc')      
      ->skip($data['count'] * $data['batch'])
      ->take($data['batch'])
      ->get();

    foreach($forums_old as $forum_old) {

      if (!Forum::find($forum_old->nid)) {

      $forum = new Forum;
      $forum->id = $forum_old->nid;
      $forum->forum_type = 'General';
      $forum->user_id = $forum_old->uid;
      $forum->title = $forum_old->title;
      $forum->body = $forum_old->body;

      $forum->created_at = Carbon::createFromTimeStamp($forum_old->created);  
      $forum->updated_at = Carbon::createFromTimeStamp($forum_old->last_comment);  

      // @TODO Use join
      
      $topic_blog = DB::connection('trip')
       ->table('term_node')
       ->where('term_node.nid', '=', $forum_old->nid)
       ->whereIn('term_node.tid', array(821, 825, 763)) // Reisikirjad, Soodus, Reisiveeb
       ->first();

      if (!$topic_blog) {
         $forum->save();  
         $this->createUser($forum_old->uid);
         $this->createComments($forum_old->nid, 'Forum');
         $this->attachDestinations($forum_old->nid, 'Forum');                     
         $this->attachTopics($forum_old->nid, 'Forum');     
         $this->attachFlags($forum_old->nid, 'Forum');
                             

      }    

     }

   }

   $job->delete();

  }
 
        
}