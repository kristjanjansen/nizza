<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertForum extends ConvertBase {
	
	protected $name = 'nizza:convert-forum';
  
	public function __construct(Filesystem $files)
	{
		parent::__construct($files);
	}
	
	public $topicMap = array(
    'Konkurss' => array(
      'delete' => true
    ),
    'Trip.ee tänab' => array(
      'move' => 773 // Trip.ee tagasiside
    ),
    'Up Traveli reisijutu konkurss' => array(
      'move' => 773
    ),
    'Luksusreis' => array(
      'delete' => true
    ),
    'Paadimatk' => array(
      'move' => 4368 // Matkamine
    ),
    'Jalgsimatk' => array(
      'move' => 4368
    ),
    'Toidu-joogireis' => array(
      'rename' => 'Söök ja jook'
    ),
    'Mägimatk' => array(
      'move' => 4368
    ),
    'Sukeldumisreis' => array(
      'rename' => 'Sukeldumine'
    ),
    'Reisivaluuta' => array(
      'move' => 831 // Hinnad kohapeal
    ),  
    'Reisikaardid' => array(
      'move' => 704 // Reisiraamatud
    ),
    'Reisiveeb' => array( // 763
      'type' => 'ForumMisc'
    ),
    'Autahvel' => array(
      'delete' => true
    ),
    'Reisiraamatud' => array(
      'rename' => 'Reisijuhid ja kaardid'
    ),
    'Trip.ee tagasiside' => array(
      'rename' => 'Trip.ee'
    ),
    'Reisimeditsiin' => array(
      'rename' => 'Meditsiin'
    ),
    'Jalgrattamatk' => array(
      'move' => 4368
    ),
    'Reisifoto' => array(
      'rename' => 'Foto'
    ),
    'Lendude soodukad' => array(
      'type' => 'Flight' // 825
    ),
    'Kultuurireis' => array(
      'delete' => true
    ),
    'Laevareis' => array(
      'rename' => 'Laevad ja kruiisid'
    ),
    'Hinnad kohapeal' => array(
      'rename' => 'Raha ja hinnad'
    ),
    'Häälega reis' => array(
      'move' => 516 // Seljakotireis
    ),
    'Inimesed' => array(
      'rename' => 'Kohalikud inimesed'
    ),
    'Lastega reis' => array(
      'rename' => 'Lastega reisimine'
    ),
    'Seljakotireis' => array(
      'rename' => 'Seljakotireis ja hääletamine'
    ),
    'Reisivarustus' => array(
      'rename' => 'Varustus'
    ),
    'Reisidokumendid' => array(
      'rename' => 'Viisad'
    ),
    'Reisiideed' => array(
      'delete' => true
    ),    
    'Reisiöömaja' => array(
      'rename' => 'Öömaja'
    ),
    'Reisikiri' => array( // 821
      'type' => 'Blog'
    ),
    'Auto-motoreis' => array(
      'rename' => 'Autoreis'
    ),
    'Lemmikloom reisil' => array(
      'new' => true,
      'id' => 5000,
      'pattern' => '/(lemmikloom|koer|kass)/i'
    ),
    'GPS' => array(
      'new' => true,
      'id' => 5001,
      'pattern' => '/GPS/'
    ),
    'Autorent' => array(
      'new' => true,
      'id' => 5003,
      'pattern' => '/(autorent|rendia|renti)/i'
    ),
    'Motoreis' => array(
      'new' => true,
      'id' => 5004,
      'pattern' => '/(mootor|moto)/i'
    ),
    'Turvalisus' => array(
      'new' => true,
      'id' => 5005,
      'pattern' => '/(turval)/i'
    ),
  );
  
  
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
      $forum->type = get_class($forum);
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
         $this->attachDestinations($forum_old->nid);                     
         $this->attachTopics($forum_old->nid); 
         
         $this->attachFlags($forum_old->nid, 'Forum');
                             

      }    

     }

   }

   $job->delete();

  }
 
	public function createTopics() {

    $topics_old = DB::connection('trip')
      ->table('term_data')
      ->where('vid', '=', 5) // Reisistiilid
      ->orWhere('vid', '=', 9) // Rubriigid
      ->get();

    foreach($topics_old as $topic_old) {
      
      $count = DB::connection('trip')
        ->table('term_node')
        ->where('tid', '=', $topic_old->tid)
        ->count();        
      
      if (!Topic::find($topic_old->tid)) {
      
          $topic = new Topic;
          $topic->id = $topic_old->tid;
          $topic->title = $topic_old->name;
          if (
            array_key_exists($topic_old->name, $this->topicMap) && 
            array_key_exists('rename', $this->topicMap[$topic_old->name])
          ) {
            $topic->title = $this->topicMap[$topic_old->name]['rename'];
          }
      //    $topic->count = $count;
          if (!array_key_exists($topic_old->name, $this->topicMap)) {
            $topic->save();
          } else {
            if (
              !array_key_exists('delete', $this->topicMap[$topic_old->name]) &&
              !array_key_exists('move', $this->topicMap[$topic_old->name]) &&
              !array_key_exists('type', $this->topicMap[$topic_old->name])
            ) {
              $topic->save();
            }
          }
        
      
      }      
      
      
		}

		foreach($this->topicMap as $key => $topic) {
		  if (array_key_exists('new', $topic)) {
		    $t = new Topic;
        $t->id = $topic['id'];
        $t->title = $key;
    		$t->save();
		  }
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
    
    
 		foreach($this->topicMap as $topic) {
 		  if (array_key_exists('new', $topic)) {
 		    $forum = Forum::find($forum_id);
         if (preg_match($topic['pattern'], $forum->title)) {
           print_r($forum->title);

           DB::table('topic_map')->insert(
             array(
               'content_id' => $forum_id, 
               'topic_id' => $topic['id']
             )
           );

         }
                  
 		  }
 		}
     
   }
         
}