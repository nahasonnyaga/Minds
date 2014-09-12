<?php
/**
 * Minds clusters controller
 */
namespace minds\core;

use minds\entities;

class clusters extends base{
	
	public $seeds = array('https://www.minds.io');
	public $ttl = 1800; //nodes live for half an hours, and then they have to reconfirm
		
	/**
	 * Init
	 */
	public function init(){
		
		/**
		 * Register our page end points
		 */
		$path = "minds\\pages\\clusters";
		router::registerRoutes(array(
			"/api/v1/cluster" => "$path\\index",
			"/api/v1/authenticate" => "$path\\authenticate"
		));
		
		\elgg_register_plugin_hook_handler('cron', 'halfhour', array($this, 'cron'));
		\elgg_register_plugin_hook_handler('action', 'login', array($this, 'login'));
	}
	
	/**
	 * Called every minute so we can communicate with the rest of the cluster
	 */
	public function cron(){
		
		error_log('running cron to talk to cluster');
		
		//assert our position on the network
		foreach($this->seeds as $seed){
			
			try{
				
				$response = $this->call('GET', $seed, '/api/v1/cluster/master/join', array('uri'=>elgg_get_site_url()));
				var_dump($response);
				
				$db = new data\call('user_index_to_guid');
				$db->insert('clusters:master', $response, $this->ttl);
				
				
			}catch(\Exception $e){
		
				error_log('CLUSTER ERROR: '.$e);
				
			}
			
		}
		
	}
	
	public function login(){
		//check if the select node is this one or not. 
		$node_uri = \get_input('node');
		if("https://$node_uri" == elgg_get_site_url() || "http://$node_uri" == elgg_get_site_url()){
			return true;
		}
		
		$username = get_input('username');
		$password = get_input('password');
		
		/**
		 * Confirm autorization from the other node
		 */
		try{
		 	$authenticate = $this->call('POST', $node_uri, 'api/v1/authenticate', array('username'=>$username, 'password'=>$password));
		}catch(\Exception $e){

			//$db = new data\call('user_index_to_guid');
			//$db->removeAttributes('clusters:master', array($node_uri));
			
			\register_error('Sorry, there was an issue communicating with the host');
			return false;
		}

		
		if($authenticate['error']){
			\register_error('Sorry, we could not succesfully authenticate you.');
			return false;
		}
		/**
		 * Now create a pseudo account and import information from the user
		 * 
		 * @todo maybe integrate OAuth2.0 at the point
		 */
		$user = new entities\user($authenticate['guid']);
		if(!$user->username){
			while(get_user_by_username($username)){
				$username .= rand(1000,9000);
			}
			$user->username = $username;
			$user->email = $authenticate['email'];
			$user->base_node = $node_uri;
			$user->salt = generate_random_cleartext_password(); // Note salt generated before password!
			$user->password = generate_user_password($user, generate_random_cleartext_password()); //random password because this isn't actually a user registered here
			$user->save();
		}
		
		\login($user);
		
		return false; //it has to be false for some odd reason.
	}
	
	/**
	 * Call
	 * 
	 * @description Vital for inter-node communications
	 */
	public function call($method, $address, $endpoint, array $data = array()){
		
		$ch = curl_init();

		switch (strtolower($method)) {
		    case 'post':
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;
		    case 'delete':
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); // Override request type
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$endpoint .= '?' . http_build_query($data); //because post fields can not be sent with DELETE
				break;
		    case 'get':
		    default:
				curl_setopt($ch, CURLOPT_HTTPGET, true);
				$endpoint .= '?' . http_build_query($data);
				break;
		}

		curl_setopt($ch, CURLOPT_URL, $address . $endpoint);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		//curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Minds Clusters v1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		$result = curl_exec($ch);
		$errors = curl_error($ch);
		
		if($errors){
			throw new \Exception($errors);
		}
	
		return json_decode($result, true);	
		
	}
	
	public function joinCluster($cluster, $server_uri){
		//notify everyone in the cluster
	}
		
}