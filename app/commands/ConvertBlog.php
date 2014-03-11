<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertBlog extends ConvertBase {
	
	protected $name = 'nizza:convert-blog';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
	public function fire() {
    
    $this->createDestinations();
		
    for ($count = 0; $count < $this->max_count; $count ++) {
      
      $this->info('Pushed ' . $count + 1 . ' set of blogs to queue');
      
        Queue::push('ConvertBlog@convert', array(
        'count' => $count,
        'batch' => $this->batch,
      ));
  
    }
		  
	}
	
  public function convert($job, $data) {
      
    // From blogs
    
    $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/";
    
    $blogs_old = DB::connection('trip')
      ->table('node')
      ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
      ->select('*', 'node.uid')      
      ->where('node.status', '=', 1)
      ->where('node.type', '=', 'trip_blog')
      ->orderBy('node.created', 'desc')
      ->skip($data['count'] * $data['batch'])
      ->take($data['batch'])         
      ->get();

    foreach($blogs_old as $blog_old) {

      if (!Blog::find($blog_old->nid)) {

      $blog = new Blog;
      $blog->id = $blog_old->nid;
      $blog->user_id = $blog_old->uid;
      $blog->title = $blog_old->title;
      $blog->body = $blog_old->body;

      if (preg_match_all($pattern, $blog->body, $matches)) {
        $blog->url = $matches[0][0];
      }
      
      $blog->created_at = Carbon::createFromTimeStamp($blog_old->created);  
      $blog->updated_at = Carbon::createFromTimeStamp($blog_old->last_comment);  
      
      $blog->save();
     
      $this->createUser($blog_old->uid);
      $this->createComments($blog_old->nid, 'Blog');
      $this->attachDestinations($blog_old->nid, 'Blog');                     
      
      }
    }
    
    // From Forum
    
		$blogs_old = DB::connection('trip')
     ->table('node')
     ->join('node_revisions', 'node_revisions.nid', '=', 'node.nid')
     ->join('term_node', 'term_node.nid', '=', 'node.nid')
     ->select('*', 'node.uid')     
     ->where('node.status', '=', 1)
     ->where('node.type', '=', 'trip_forum')
     ->where('term_node.tid', '=', 821)
     ->orderBy('node.created', 'desc')
     ->skip($data['count'] * $data['batch'])
     ->take($data['batch'])                   
     ->get();

   foreach($blogs_old as $blog_old) {

     $blog = new Blog;
     $blog->id = $blog_old->nid;
     $blog->user_id = $blog_old->uid;
     $blog->title = $blog_old->title;
     $blog->body = '(from forum) ' . $blog_old->body;
     
     if (preg_match_all($pattern, $blog->body, $matches)) {
       $blog->url = $matches[0][0];
     }
     
     $blog->created_at = Carbon::createFromTimeStamp($blog_old->created);  
     $blog->updated_at = Carbon::createFromTimeStamp($blog_old->last_comment);  
     
     $blog->save();

     $this->createUser($blog_old->uid);
     $this->createComments($blog_old->nid, 'Blog');
     $this->attachDestinations($blog_old->nid, 'Blog');                     

   }
    
   // From node
   
   $comments_old = DB::connection('trip')
     ->table('comments')
     ->where('nid', '=', 38033)
     ->get();

   foreach($comments_old as $comment_old) {
     $blog = new Blog;
 //    $blog->id = $comment_old->cid + 1000000000000;
     $blog->user_id = $comment_old->uid;
     $blog->title = $comment_old->subject;
     $blog->body = '(from node) ' . $comment_old->comment;
     $blog->created_at = Carbon::createFromTimeStamp($comment_old->timestamp);  
     $blog->updated_at = Carbon::createFromTimeStamp($comment_old->timestamp);  

     if (preg_match_all($pattern, $blog->body, $matches)) {
       $blog->url = $matches[0][0];
     }
     
     $blog->save();

     $this->createUser($comment_old->uid);

	}
	    
   $job->delete();

  }

        
}