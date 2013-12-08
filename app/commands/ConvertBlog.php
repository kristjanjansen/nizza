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
    $this->createTopics();
		
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
      $blog->type = get_class($blog);
      $blog->user_id = $blog_old->uid;
      $blog->title = $blog_old->title;
      $blog->body = $blog_old->body;
      $blog->created_at = Carbon::createFromTimeStamp($blog_old->created);  
      $blog->updated_at = Carbon::createFromTimeStamp($blog_old->last_comment);  
      
      $blog->save();

      $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/";
      if (preg_match_all($pattern, $blog->body, $matches)) {
        $fields = new BlogField;
        $fields->content_id = $blog_old->nid;
        $fields->url = $matches[0][0];
        $fields->save();  
      }
      
      
      $this->createUser($blog_old->uid);
      $this->createComments($blog_old->nid, 'Blog');
      $this->attachDestinations($blog_old->nid);                     
      
      }
    }
    
    // From forum
    
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
     $blog->created_at = Carbon::createFromTimeStamp($blog_old->created);  
     $blog->updated_at = Carbon::createFromTimeStamp($blog_old->last_comment);  
     
     $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/";
     if (preg_match_all($pattern, $blog->body, $matches)) {
       $fields = new BlogField;
       $fields->content_id = $blog_old->nid;
       $fields->url = $matches[0][0];
       $fields->save();
     }
     
     $blog->save();

     $this->createUser($blog_old->uid);
     $this->createComments($blog_old->nid, 'Blog');
     $this->attachDestinations($blog_old->nid, 'Blog');                     

   }
    
  
   $comments_old = DB::connection('trip')
     ->table('comments')
     ->where('nid', '=', 38033)
     ->get();

   foreach($comments_old as $comment_old) {
     $blog = new Blog;
     $blog->id = $comment_old->cid;
     $blog->user_id = $comment_old->uid;
     $blog->title = $comment_old->subject;
     $blog->body = '(from list) ' . $comment_old->comment;
     $blog->created_at = Carbon::createFromTimeStamp($comment_old->timestamp);  
     $blog->updated_at = Carbon::createFromTimeStamp($comment_old->timestamp);  


     $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/";
     if (preg_match_all($pattern, $blog->body, $matches)) {
       $fields = new BlogField;
       $fields->content_id = $comment_old->nid;
       $fields->url = $matches[0][0];
       $fields->save();
     }
     
     $blog->save();

     $this->createUser($comment_old->uid);

	}
	    
   $job->delete();

  }

        
}