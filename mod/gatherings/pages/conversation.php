<?php
/**
 * Gatherings page handler
 */
namespace minds\plugin\gatherings\pages;

use minds\core;
use minds\interfaces;
use minds\plugin\gatherings\entities;
use minds\plugin\gatherings\helpers;
use minds\plugin\gatherings\counter;

class conversation extends core\page implements interfaces\page{
	
	public $context = 'gatherings';
	private $passphrase = NULL;
	
	/**
	 * Reading messages and getting lists of messages
	 */
	public function get($pages){
		if(!isset($pages[0]) && get_input('username') || $pages[0] == 'new')
			$pages[0] = get_input('username');

		counter::clear();

		$user = new \minds\entities\user($pages[0]);
		
		$show = true;
		$option = \elgg_get_plugin_user_setting('option', elgg_get_logged_in_user_guid(), 'gatherings');
		
		if((int)$option == 1 && !$this->passphrase && (!isset($_SESSION['tmp_privatekey']) || !isset($_COOKIE['tmp_priv_pswd']))){
			//we need a password from the user...
			$content = elgg_view_form('message_unlock', array('action'=>elgg_get_site_url() . 'gatherings/conversation/'.$pages[0].'/unlock'));
			
			$show = false;
			
			if(isset($_SESSION['tmp_privatekey'])){
				unset($_SESSION['tmp_privatekey']);
				unset($_SESSION['tmp_privatekey_ts']);
			}
		}
		
		if($show){
			$convseration = new entities\conversation(elgg_get_logged_in_user_guid(), $user->guid);
			$a = elgg_get_logged_in_user_guid();
			$b = $user->guid;
			$guids = core\data\indexes::fetch("object:gathering:conversation:$a:$b");
			if($guids){
				$messages = core\entities::get(array('guids'=>$guids));
				foreach($messages as $k => $message){
					$messages[$k] = new entities\message($message, $this->passphrase);
					//var_dump($message->decryptMessage());
				}
				$messages = array_reverse($messages);
				$content = elgg_view('gatherings/conversation', array('conversation'=>$conversation, 'messages'=>$messages));
			}
			$content .= elgg_view_form('conversation', array('action'=>elgg_get_site_url() . 'gatherings/conversation/'.$user->guid), array('encrypted'=>$encrypted,'user'=>$user));
		}
		
		$conversations = \minds\plugin\gatherings\start::getConversationsList();

		$layout = elgg_view_layout('one_sidebar_alt', array('content'=>$content, 'sidebar'=>elgg_view('gatherings/conversations/list', array('conversations'=>$conversations))));
		echo $this->render(array('body'=>$layout));
		
	}
	
	/**
	 * Posting messages 
	 */
	public function post($pages){
		
		if(isset($pages[1]) && $pages[1] == 'unlock'){
			$this->passphrase = get_input('passphrase');
			
			$new_pswd = base64_encode(openssl_random_pseudo_bytes(128));
			$tmp = helpers\openssl::temporaryPrivateKey(\elgg_get_plugin_user_setting('privatekey', elgg_get_logged_in_user_guid(), 'gatherings'), $this->passphrase, $new_pswd);
			$_SESSION['tmp_privatekey'] = $tmp;
			$_SESSION['tmp_privatekey_ts'] = time();
			
			setcookie('tmp_priv_pswd', $new_pswd, time() + (60 * 60 * 60 * 24), '/', NULL, NULL, true);
			return $this->get($pages);
		}
		
		$conversation = new entities\conversation(elgg_get_logged_in_user_guid(), get_input('user_guid'));
		
		$message = new entities\message($conversation);
		$message->setMessage(get_input('message'))
				->save();

		$conversation->update();		
		$this->forward(REFERRER);
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
