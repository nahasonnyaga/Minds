<?php
/**
 * View an avatar
 */

$user = elgg_get_page_owner_entity();

// Get the size
$size = strtolower(get_input('size'));
if (!in_array($size, array('master', 'large', 'medium', 'small', 'tiny', 'topbar'))) {
	$size = 'medium';
}

// If user doesn't exist, return default icon
if (!$user) {
	$url = "_graphics/icons/user/default{$size}";
	$url = elgg_normalize_url($url);
	forward($url);
}

// Try and get the icon
$filehandler = new ElggFile();
$filehandler->owner_guid = $user->getGUID();
$filehandler->setFilename("profile/" .  $user->getGUID() . $size . ".jpg");

$success = false;

try {
	if ($filehandler->open("read")) {
		if ($contents = $filehandler->read($filehandler->size())) {
			$success = true;
		}
	}
} catch (InvalidParameterException $e) {
	elgg_log("Unable to get profile icon for user with GUID $entity->guid", 'ERROR');
}


if (!$success) {
	$url = "_graphics/icons/user/default{$size}.gif";
	$url = elgg_normalize_url($url);
	forward($url);
}

header("Content-type: image/jpeg");
header('Expires: ' . date('r', time() + 864000));
header("Pragma: public");
header("Cache-Control: public");
header("Content-Length: " . strlen($contents));

echo $contents;
