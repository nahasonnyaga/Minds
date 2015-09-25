<?php
/**
 * Album entity
 *
 * Albums are containers for other entities and also act as PAM controllers
 */
namespace minds\plugin\archive\entities;

use minds\entities\object;
use Minds\Core\Data;

class album extends object{

	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['super_subtype'] = 'archive';
		$this->attributes['subtype'] = "album";
	}

	public function getURL(){
		return elgg_get_site_url() . 'archive/view/'.$this->guid;
	}

	/**
	 * Get the icon url. This is configurable to be multiple images from the album or
	 * just a specific image. It defaults the the latest image in the album
	 */
	public function getIconURL($size = 'large'){
		global $CONFIG; //@todo remove globals!
		return $CONFIG->cdn_url . 'archive/thumbnail/' . $this->guid . '/'.$size;
	}

	public function getChildrenGuids($limit = 1000000, $offset = ''){
		$index = new Data\indexes('object:container');
		return $index->get($this->guid, array('limit'=>$limit, 'offset'=>$offset));
	}

	public function getChildren(){
		//$guids = $this->getChildrenGuids();

	}

	public function addChildren($guids){
		$rows = array();
		foreach($guids as $guid){
			$rows[$guid] = array('container_guid' => $album->guid);
		}

		if($rows){
			$db = new Data\Call('entities');
			$db->insertBatch($rows);
		}

		$db = new Data\Call('entities_by_time');
		$db->insert("object:container:$this->guid", $guids);

	}

	/**
	 * Extend the default entity save function to update the remote service
	 *
	 */
	public function save($public = true){
		$this->super_subtype = 'archive';
		$this->access_id = 2;
		parent::save($public);
		return $this->guid;
	}

	/**
	 * Extend the default delete function to remove from the remote service
	 */
	public function delete(){
		return parent::delete();
		//delete all children too.
	}

	function getFilePath(){
	}

	 public function getExportableValues() {
		return array_merge(parent::getExportableValues(), array(
			'thumbnail',
			'images'
		));
	}

	 public function export(){
		$this->images = $this->getChildrenGuids();
		return parent::export();
	 }
}
