<?php
/**
 * Market items entity model.
 * 
 */
 
namespace minds\plugin\market\entities;

use Minds\Entities;

class item extends Entities\Object{
	
	public function initializeAttributes(){
		$this->attributes = array_merge($this->attributes, array(
			'title' => null,
			'type' => 'object',
			'subtype'=>'market',
			'description' => null,
			'price' => null,
			'stock' => 0, //0 = unlimited
			'access_id' => ACCESS_PUBLIC,
			'owner_guid'=> \elgg_get_logged_in_user_guid(),
			'category' => 'uncategorised'
		));
	}
	
	/**
	 * Add the item to the basket
	 */
	public function addToBasket($quantity = 1){
		$basket = new basket();
		$basket->addItem($this, $quantity);
	}
	
	/**
	 * Returns an array of the category tree from the static category property
	 * 
	 * @return array
	 */
	private function getCategoryTree(){
		$return = array($this->category);
		//$parts = explode(':',$this->category);
		$cat = $this->category;
		
		while(true){
			$cat = substr($cat, 0, strrpos( $cat, ':', -1));
			if($cat)
				$return[] = $cat;
			else break;
		}
		return $return;
	}
	/**
	 * The indexes...
	 */
	public function getIndexKeys($ia = false){
		$indexes = parent::getIndexKeys($ia);
		
		$categories = $this->getCategoryTree();
		$indexes = array_merge($indexes, $categories);
		return $indexes;
	}
	
	/**
	 * Attach previews
	 */
	public function attachPreviews(array $guids = array()){
		
	}
	
	/**
	 * Return the URL for the item
	 */
	public function getURL(){
		return \elgg_get_site_url() .'market/item/'.$this->guid;
	}
		
}