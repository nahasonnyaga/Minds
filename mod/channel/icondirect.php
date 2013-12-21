<?php
/**
 * Channel icon cache/bypass
 * 
 * 
 * @package ElggProfile
 */

// Get DB settings
require_once(dirname(dirname(dirname(__FILE__))). '/engine/settings.php');

global $CONFIG;

// won't be able to serve anything if no joindate or guid
if (!isset($_GET['joindate']) || !isset($_GET['guid'])) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

$join_date = (int)$_GET['joindate'];
$last_cache = (int)$_GET['lastcache']; // icontime
$guid = $_GET['guid'];

// If is the same ETag, content didn't changed.
$etag = $last_cache . $guid;
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
	header("HTTP/1.1 304 Not Modified");
	exit;
}

$size = strtolower($_GET['size']);
if (!in_array($size, array('xlarge', 'large', 'medium', 'small', 'tiny', 'master', 'topbar'))) {
	$size = "medium";
}

$data_root = $CONFIG->dataroot;

if (isset($data_root)) {

		// this depends on ElggDiskFilestore::makeFileMatrix()
		$user_path = date('Y/m/d/', $join_date) . $guid;

		$filename = "$data_root$user_path/profile/{$guid}{$size}.jpg";
		$contents = @file_get_contents($filename);
		if (!empty($contents)) {
			header("Content-type: image/jpeg");
			header('Expires: ' . date('r',  strtotime("today+6 months")), true);
			header("Pragma: public");
			header("Cache-Control: public");
			header("Content-Length: " . strlen($contents));
			header("ETag: $etag");
			header("X-No-Client-Cache:0");
			// this chunking is done for supposedly better performance
			$split_string = str_split($contents, 1024);
			foreach ($split_string as $chunk) {
				echo $chunk;
			}
			exit;
		}
}

// something went wrong so load engine and try to forward to default icon
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
elgg_log("Profile icon direct failed.", "WARNING");
forward("_graphics/icons/user/default{$size}.gif");
