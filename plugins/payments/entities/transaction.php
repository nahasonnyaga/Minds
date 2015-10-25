<?php
/**
 * Transaction entity
 */
 
namespace minds\plugin\payments\entities;
 
use Minds\Entities;

class transaction extends Entities\Object{
	
	/**
	 * Initialise attributes
	 * @return void
	 */
	public function initializeAttributes(){
		parent::initializeAttributes();
		$this->attributes = array_merge($this->attributes, array(
			'subtype' => 'transaction',
			'owner_guid' => elgg_get_logged_in_user_guid(),
			'access_id' => 0 //private
		));
	}
	
	

}