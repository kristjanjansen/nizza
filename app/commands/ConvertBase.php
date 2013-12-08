<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertBase extends Command {

	protected $name = 'nizza:base';

  protected $files;
  
  public $batch;
  public $max_count;
  public $image_src;
  public $image_tgt;
  public $user_image_src;
  public $user_image_tgt;
  
	public function __construct(Filesystem $files)
	{
		parent::__construct();
		$this->files = $files;
	  $this->batch = Config::get('nizza.batch');
	  $this->max_count = Config::get('nizza.max_count');
	  $this->image_src = Config::get('nizza.image_src');
	  $this->image_tgt = Config::get('nizza.image_tgt');
	  $this->user_image_src = Config::get('nizza.user_image_src');
	  $this->user_image_tgt = Config::get('nizza.user_image_tgt');
	}



	public function fire() {
	}

  // @TODO remove $type
  
 	public function createComments($forum_id, $type = null) {

     $comments_old = DB::connection('trip')
       ->table('comments')
       ->where('status', '=', 0)
       ->where('nid', '=', $forum_id)
       ->get();

     foreach($comments_old as $comment_old) {
       $comment = new Comment;
       $comment->id = $comment_old->cid;
       $comment->user_id = $comment_old->uid;
       $comment->content_id = $comment_old->nid;
       $comment->body = $comment_old->comment;
       $comment->created_at = Carbon::createFromTimeStamp($comment_old->timestamp);  
       $comment->updated_at = Carbon::createFromTimeStamp($comment_old->timestamp);  
       $comment->save();

       $this->createUser($comment_old->uid);
       
       if (in_array($type, array('Forum', 'Expat', 'Misc'))) {
         $this->attachFlags($comment_old->cid, 'Comment');
       }
       

 		}

   }

 	public function createUser($user_id) {

    // @TODO Consider adding accessed_at field
    // @TODO status

     $oldest = 936306000;
     
     $user_old = DB::connection('trip')
       ->table('users')      
       ->where('uid', '=', $user_id)
       ->first();

     if (!User::find($user_old->uid)) {

       // @TODO fix this mess
/*       
       if ($user_old->picture && file_exists($this->user_image_src . basename($user_old->picture))) {
*/           
        if ($user_old->picture) {

         $image_path = basename($user_old->picture);
         
         $this->files->copy(
            $this->user_image_src . $image_path,
            $this->user_image_tgt . $image_path,
          true);

       } else {
         $image_path = '';
       }
       
       $user = new User;
       $user->id = $user_old->uid;
       $user->name = $user_old->name;
       $user->email = $user_old->mail;
       $user->password = $user_old->pass;
       $user->image_path = $image_path;
       $user->created_at = Carbon::createFromTimeStamp($user_old->created >= $oldest ? $user_old->created : $oldest);  
       $user->updated_at = Carbon::createFromTimeStamp($user_old->access);  
       $user->save();
     }

   }
   
   
  	public function createDestinations() {

      $dests_old = DB::connection('trip')
        ->table('term_data')
        ->join('term_hierarchy', 'term_data.tid', '=', 'term_hierarchy.tid')        
        ->where('term_data.vid', '=', 6)
        ->get();

      foreach($dests_old as $dest_old) {
        if (!Destination::find($dest_old->tid)) {
          $dest = new Destination;
          $dest->id = $dest_old->tid;
          $dest->title = $dest_old->name;
          $dest->parent_id = $dest_old->parent;
          $dest->save();
        }
  		}

    }

 
  	public function createTopics() {

    }
 
    
  	public function createCarriers() {

      $carriers_old = DB::connection('trip')
        ->table('term_data')
        ->join('term_hierarchy', 'term_data.tid', '=', 'term_hierarchy.tid')        
        ->where('term_data.vid', '=', 23)
        ->get();

      foreach($carriers_old as $carrier_old) {
        if (!Carrier::find($carrier_old->tid)) {
          $carrier = new Carrier;
          $carrier->id = $carrier_old->tid;
          $carrier->title = $carrier_old->name;
          $carrier->save();
        }
  		}

    }
    
     

    // @TODO remove $type
    
   	public function attachDestinations($forum_id, $type = null) {


      // Destinations
    
      $dests_old = DB::connection('trip')
       ->table('term_node')
       ->join('term_data', 'term_node.tid', '=', 'term_data.tid')              
       ->where('term_node.nid', '=', $forum_id)
       ->where('term_data.vid', '=', 6)
       ->get();
		  
       foreach ($dests_old as $dest_old) {
         DB::table('destination_map')->insert(
             array(
              'content_id' => $forum_id, 
              'destination_id' => $dest_old->tid
              )
         );
       }

     }


  
     public function attachTopics($forum_id) {
    
       // Styles and topics
       
       $topics_old = DB::connection('trip')
        ->table('term_node')
        ->join('term_data', 'term_node.tid', '=', 'term_data.tid')              
        ->where('term_node.nid', '=', $forum_id)
        ->where(function($query) {
            $query->where('term_data.vid', '=', 5)
              ->orWhere('term_data.vid', '=', 9);
        })
        ->get();

        foreach ($topics_old as $topic_old) {
          $tid = null;
          if (!array_key_exists($topic_old->name, $this->topicMap)) {
            $tid = $topic_old->tid;
          } else {
            if (
              !array_key_exists('delete', $this->topicMap[$topic_old->name])
              &&
              !array_key_exists('type', $this->topicMap[$topic_old->name])
            ) {
              $tid = $topic_old->tid;
            }
            if (array_key_exists('move', $this->topicMap[$topic_old->name])) {
              $tid = $this->topicMap[$topic_old->name]['move'];
            }
          }
          if ($tid) {
            DB::table('topic_map')->insert(
              array(
                'content_id' => $forum_id, 
                'topic_id' => $tid
              )
            );
          }
        
        }
        
      }
      
       public function attachFlags($flaggable_id, $type) {

         $flag_map = array(
          '2' => 'good',
          '3' => 'bad',
          '4' => 'good',
          '5' => 'bad'
         );
         
      		$flags_old = DB::connection('trip')
           ->table('flag_content')
           ->where('content_id', $flaggable_id)
           ->orderBy('timestamp', 'desc')
           ->get();

        
         foreach($flags_old as $flag_old) {

           $flag = new Flag;
           $flag->id = $flag_old->fcid;
           $flag->user_id = $flag_old->uid;
           $flag->flaggable_id = $flag_old->content_id;
           $flag->flaggable_type = $type;


           $flag->created_at = Carbon::createFromTimeStamp($flag_old->timestamp);  
           $flag->updated_at = Carbon::createFromTimeStamp($flag_old->timestamp);  

           if (isset($flag_map[$flag_old->fid])) {
             $flag->flag_type = $flag_map[$flag_old->fid];
             $flag->save();
             $this->createUser($flag_old->uid);
           }

         }
 
 
    }

}