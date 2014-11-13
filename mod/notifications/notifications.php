<?php
/**
 * Minds Notifications
 */
 
namespace minds\plugin\notifications;

use minds\core;

class notifications extends \ElggPlugin{
	
	/**
	 * Initialise the plugin
	 */
	public function init(){
		\elgg_register_plugin_hook_handler('cron', 'minute', array($this, 'cronHandler'));
		\elgg_register_plugin_hook_handler('cron', 'daily', array($this, 'cronHandler'));
		\elgg_register_plugin_hook_handler('cron', 'weekly', array($this, 'cronHandler'));
		\add_subtype('notificaiton', 'email', 'ElggNotificationEmail');
		
		\elgg_register_plugin_hook_handler('entities_class_loader', 'all', function($hook, $type, $return, $row){
			//var_dump($row);
			if($row->type == 'notification')
				return new entities\notification($row);
		});

		core\router::registerRoutes($this->registerRoutes());

		elgg_register_event_handler('pagesetup', 'system', 'notifications_plugin_pagesetup');
		elgg_register_event_handler('pagesetup', 'system', array($this, 'pageSetup'));

		// Unset the default notification settings
		\elgg_unregister_plugin_hook_handler('usersettings:save', 'user', 'notification_user_settings_save');
	
		\elgg_register_plugin_hook_handler('notification', 'all', array($this,'createNotification'));
		
		//\elgg_register_event_handler('create', 'object', 'notifications_notify');
		
		\elgg_extend_view('js/elgg','js/notifications/notify');
		\elgg_extend_view('css/elgg','notifications/css');
		
		\elgg_register_event_handler('create', 'all', array($this, 'createHook'));
		
	
		/*$actions_base = elgg_get_plugins_path() . 'notifications/actions';
		elgg_register_action("notificationsettings/save", "$actions_base/save.php");
		elgg_register_action("notificationsettings/groupsave", "$actions_base/groupsave.php");*/
	}
	
	/**
	 * Page registrations
	 * 
	 * @return bool
	 */
	public function registerRoutes(){
		$path = "minds\\plugin\\notifications";
		return array(
			'/notifications' => "$path\\pages\\view",
		);
	}
	
	/**
	 * Notifications pagesetup
	 * - Adds the 'notifier' icon to the header
	 */
	public function pageSetup(){
		if (\elgg_is_logged_in()) {
			
			\elgg_extend_view('page/elements/topbar', 'notifications/popup');
			
			$class = "notification notifier entypo";
			$text = "<span class='$class'>&#59141;</span>";
			$tooltip = \elgg_echo("notification");
			
			// get unread messages
			$num_notifications = $this->getCount();
			if ($num_notifications > 0) {
				$class = "notification notifier entypo new";
				$text = "<span class='$class'>&#59141;" .
							"<span class=\"notification-new\">$num_notifications</span>" .
						  "</span>";
				$tooltip .= " (" . \elgg_echo("notifications:unread", array($num_notifications)) . ")";
			}
	
			\elgg_register_menu_item('notifications', array(
				'name' => 'notification',
				'href' => '#notification',
				'rel' => 'popup',
				'text' => $text,
				'priority' => 600,
				'class' => 'entypo',
				'title' => $tooltip,
				'id'=>'notify_button',
				'section' => 'alt',//this is custom to the minds theme. 
			));
		}
	}
	
	/**
	 * Return a count of notifications
	 * @return int
	 */
	public function getCount(){
		$user = \elgg_get_logged_in_user_entity();
		return $user->notifications_count;	
	}
	
	/**
	 * Increase a users notification counter
	 */
	 public function increaseCounter($user_guid){
	 	try{
		 	elgg_set_ignore_access(true);
			$user = new \ElggUser($user_guid);
			if($user){
				$user->notifications_count++;
				$user->save();
			}
			elgg_set_ignore_access(false);
		}catch(Exception $e){
			var_dump($e); exit;
		}
	 }
	 
	 /**
	  * Reset user notification counter
	  */
	 public function resetCounter($user_guid = NULL){
	 	try{
		 	if(!$user_guid)
				$user_guid = elgg_get_logged_in_user_guid();
		
			elgg_set_ignore_access(true);
			$user = new \ElggUser($user_guid);
			$user->notifications_count = 0;
			$user->save();
			elgg_set_ignore_access(false);
		}catch(Exception $e){}
	 }
	
	/**
	 * Return a list of notifications
	 * @return array
	 */
	public function getNotifications($user = NULL, $limit = 12, $offset = ""){
		if(!$user)
			$user = \elgg_logged_in_user_entity();
		
		return elgg_get_entities(array('attrs'=>array('namespace'=>'notifications:'.$user->guid), 'limit'=>$limit,'offset'=>$offset ));
	}
	
	
	/**
	 * Create a new notification
	 * 
	 */
	public function createNotification($hook, $type, $return, $params = array()){
		$defaults = array(
			'to' => array(),
			'from' => \elgg_get_logged_in_user_guid(),
			'object_guid'=> NULL
		);
		$params = array_merge($defaults, $params);

		foreach($params['to'] as $t){
		//	if($t != $params['from']){
				$notification = new entities\notification();
				$notification->to_guid = (int)$t;
				$notification->object_guid = $params['object_guid'];
				$notification->from_guid = $params['from'];
				$notification->notification_view = $params['notification_view'];
				$notification->description = $params['description'];
				$notification->read = 0;
				$notification->access_id = 2;
				$notification->owner_guid = \elgg_get_logged_in_user_guid();
				$notification->params = serialize($params['params']);
				$notification->time_created = time();
				$notification->save();
		//	}
		}
		return $return;
	}
	
	/**
	 * Notifications cron handler
	 * @return void
	 */
	public function cronHandler($hook, $type, $params, $return){
		/**
		 * FOR SECURITY ONLY ALLOW THIS TO BE CALLED FROM LOCALHOST!
		 */
		if($_SERVER['HTTP_HOST'] != 'localhost'){
			return false;
		}
		
		$queue = \elgg_get_entities(array('type'=>'notification', 'subtype'=>'email', 'limit'=>0));
		
		foreach($queue as $q){
			if($q->send()){
				echo 'sent';
			} else {
				if($q->state == 'completed' && $q->time_created <= time()-3600){
					$q->delete();
				}
				echo $q->state;
			}
		}
		
		$mail = new \ElggNotificationEmail();
		switch($type){
			case 'daily':
				$mail->subject = 'Your Minds Headlines';
				$mail->subscription = $type;
				$mail->send();
				break;
			case 'weekly':
				$mail->subject = 'Your Minds Headlines';
				$mail->subscription = $type;
				$mail->send();
				break;
		}
	}
	
	/**
	 * Create hook
	 * @return void
	 */
	public function createHook($hook, $type, $params, $return){
		if($type == 'activity'){
			if (preg_match_all('!@(.+)(?:\s|$)!U', $params->message, $matches)){
				$usernames = $matches[1];
				$to = array();
				foreach($usernames as $username){
					$user= new \minds\entities\user($username);
					if($user->guid)
						$to[] = $user->guid;
				}
				if($to)
					\elgg_trigger_plugin_hook('notification', 'activity', array(
						'to'=>$to, 
						'object_guid' => $params->guid,
						'notification_view' => 'tag',
						'description' => $params->message
						));
			}
		}
	}
	
}
