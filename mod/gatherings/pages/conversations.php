<?php
/**
 * Gatherings page handler
 */
namespace minds\plugin\gatherings\pages;

use minds\core;
use minds\interfaces;
use minds\plugin\gatherings\entities;
use minds\plugin\gatherings\helpers;

class conversations extends core\page implements interfaces\page{
	
	public $context = 'gatherings';
	
	/**
	 * Reading messages and getting lists of messages
	 */
	public function get($pages){
		
		$content = elgg_view('gatherings/conversations/welcome');
		
		$conversations = \minds\plugin\gatherings\start::getConversationsList();	
				
		$layout = elgg_view_layout('one_sidebar_alt', array('content'=>$content, 'sidebar'=>elgg_view('gatherings/conversations/list', array('conversations'=>$conversations))));
		echo $this->render(array('body'=>$layout));
		
	}
	
	/**
	 * Posting messages 
	 */
	public function post($pages){
		$type = $pages[0];
		$parent_guid = $pages[1];
		$parent_entity = \get_entity($parent_guid);
		$ia = \elgg_set_ignore_access(true);
		
		$desc = $_POST['comment'];
		
		if (!\elgg_is_logged_in()){
			exit;	
			//relies on the minds user account being created @todo fix this?
			$owner = new \ElggUser('minds');
		
			if (false !== strpos($desc, 'http')){
				exit; //most probably spam
			}
		
		}else {
			$owner = elgg_get_logged_in_user_entity();
		}
		
		$comment = new entities\comment();
		$comment->description = $desc;
		$comment->parent_guid = $parent_guid;
		if($comment->save()){
		
			\elgg_trigger_plugin_hook('notification', 'all', array(
				'to' => array($parent_entity->owner_guid),
				'object_guid'=>$parent_guid,
				'description'=>$desc,
				'notification_view'=>'comment'
			));
			
			\elgg_trigger_event('comment:create', 'comment', $data); 
			
			\elgg_set_ignore_access($ia);
			
			echo "<li class=\"minds-comment\">";
			echo $comment->view();
			echo "</li>";
		}
		exit;
	}
	
	/**
	 * Uploading content via messages (coming soon)
	 */
	public function put($pages){}
	
	/**
	 * Deleting messages
	 */
	public function delete($pages){
		$comment = new entities\comment($pages[0]);
		if($comment->delete())
			echo 'true';
		else 
			echo false;
	}
	
}
