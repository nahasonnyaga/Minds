<?php
/**
 * Live chat helpers
 */
namespace minds\plugin\gatherings\pages;

use minds\core;
use minds\interfaces;
use minds\plugin\gatherings\entities;

class live extends core\page implements interfaces\page{
	
	public $context = 'gatherings';
	

	public function get($pages){

		
	}
	
	public function post($pages){
				
		switch($pages[0]){
			case "userlist":
				
				/**
				 * Filters a user list to return only subscriptions
				 */
				$guids = $_POST['guids'];
				$mutuals = array();
				
				//$friends = new core\data\call('friends');
				//$friends = $friends->getRow(elgg_get_logged_in_user_guid(), array('limit'=>10000));
				$friendsof = new core\data\call('friendsof');
				$friendsof = $friendsof->getRow(elgg_get_logged_in_user_guid(), array('limit'=>10000));
				
				foreach($guids as $guid){
					if(isset($friendsof[$guid]))
						$mutuals[] = $guid;
				}
				
				echo json_encode($mutuals);
			
				break;
		}
	}

	public function put($pages){}

	public function delete($pages){}
	
}
