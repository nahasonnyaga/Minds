<?php
/**
* Kaltura video client
* @package ElggKalturaVideo
* @license http://www.gnu.org/licenses/gpl.html GNU Public License version 3
* @author Ivan Vergés <ivan@microstudi.net>
* @copyright Ivan Vergés 2010
* @link http://microstudi.net/elgg/
**/

require_once($CONFIG->pluginspath."kaltura_video/kaltura/api_client/includes.php");
gatekeeper();

$delete_video = get_input('delete_video');

if($delete_video) {
	$error = '';
	$code = '';

	$ob = kaltura_get_entity($delete_video);

	try {
		//check if belongs to this user (or is admin)
		if($ob->canEdit()) {
			$kmodel = KalturaModel::getInstance();
			//open the kaltura list without admin privileges
			$entry = $kmodel->getEntry ( $delete_video );
			if($entry instanceof KalturaMixEntry) {
				//deleting media related
				//TODO: MAYBE should ask before do this!!!
				$list = $kmodel->listMixMediaEntries($delete_video);
				//print_r($list);die;
				foreach($list as $subEntry) {
					$kmodel->deleteEntry($subEntry->id);
				}
				//Delete the mix
				$kmodel->deleteEntry ( $delete_video );
				$ob = kaltura_get_entity($delete_video);
				if($ob) $ob->delete();
				system_message(str_replace("%ID%",$delete_video,elgg_echo("kalturavideo:action:deleteok")));
			}
			else {
				$error = str_replace("%ID%",$delete_video,elgg_echo("kalturavideo:action:deleteko"));
			}
		}
		else {
			$error = elgg_echo('kalturavideo:edit:notallowed');
		}
	}
	catch(Exception $e) {
		$code = $e->getCode();
		$error = $e->getMessage();
	}

	if( $code == 'ENTRY_ID_NOT_FOUND') {
		//we can delete the elgg object
		$ob = kaltura_get_entity($delete_video);
		if($ob instanceOf ElggObject) {
			$ob->delete();
		}
		system_message(str_replace("%ID%",$delete_video,elgg_echo("kalturavideo:action:deleteok")));
	}

	if($error) {
		register_error($error);
	}
}

$url = $_SERVER['HTTP_REFERER'];
if(strpos($url,'/kaltura_video/') === false) $url = $CONFIG->url.'pg/kaltura_video/';
if(!$error && strpos($url,'/show/')!==false) $url = $CONFIG->url.'pg/kaltura_video/';
forward($url);

?>
