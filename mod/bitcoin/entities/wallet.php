<?php
/**
 * Wallet entity
 */
 
namespace minds\plugin\bitcoin\entities;
 
use minds\entities;
use minds\plugin\bitcoin\services\blockchain;

class wallet extends entities\object{
	
	protected $cache = false; //because we load from the user..
	public $password = NULL;
	
	/**
	 * Initialise attributes
	 * @return void
	 */
	public function initializeAttributes(){
		parent::initializeAttributes();
		$this->attributes = array_merge($this->attributes, array(
			'subtype' => 'wallet',
			'owner_guid' => elgg_get_logged_in_user_guid(),
			'access_id' => 0 //private
		));
	}

	/**
	 * Create the wallet
	 */
	public function create($password = 'temp123abc'){
		$return = blockchain::__make_call('GET', "api/v2/create_wallet", array(
		    'password' => $password
		));

		if ($return['response'] != 200)
			throw new \Exception("Bitcoin: "  . $return['error']);
		
		
		$this->blockchain_guid = $return['content']['guid'];
		$this->address = $return['content']['address'];
		
		$guid = $this->save();
		
		\elgg_set_plugin_user_setting('wallet_guid', $guid, $this->owner_guid, 'bitcoin');
		
		return $guid;
	}
	
	/**
	 * Get balance
	 */
	public function balance(){
		$response = blockchain::__make_call('GET', "merchant/$this->blockchain_guid/balance", array(
			'password' => $this->password
		));
		
		if ($response['content']['error'] != ''){
			unset($_COOKIE['bitcoin_pswd']);
			setcookie('bitcoin_pswd', 'void', time()-100, '/');
			
			throw new \Exception('Balance error');
		}
		
		return $response['content']['balance'];
	}
	
	public function send($to_address, $amount = 0, $password){
		$response = blockchain::__make_call('GET', "merchant/$this->blockchain_guid/payment", array(
		    'password' => $password,
		    
		    'to' => $to_address,
		    'amount' => $amount
		));
		
		$transaction = new transaction(array(
			'owner_guid' => $this->owner_guid,
			'to_address' => $to_address,
			'amount' => $amount
		));
		$transaction->save();

	}
	
	
}