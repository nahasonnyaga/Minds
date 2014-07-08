<?php

// Get the current page's owner
$page_owner = elgg_get_page_owner_entity();
if ($page_owner === false || is_null($page_owner)) {
	$page_owner = elgg_get_logged_in_user_entity();
	elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
}

// Get the post, if it exists
$guid =  get_input('guid');
$entryid = get_input('entryid');

$entity = get_entity($guid, 'object');

if ($entity && !$entity->canEdit()) {
	forward(REFERRER);
	register_error('Sorry, you don\'t have permission');
}

	$title = elgg_view_title(elgg_echo('kalturavideo:label:adminvideos').": ".elgg_echo('kalturavideo:label:editdetails'));
	$form = elgg_view_form('archive/save', array('enctype' => 'multipart/form-data'), array('entity' => $entity));
	$body = elgg_view_layout("edit_layout", array('title'=>$title, 'content'=>$form));

// Display page
echo elgg_view_page(sprintf(elgg_echo('kalturavideo:label:adminvideos').": ".elgg_echo('kalturavideo:label:editdetails'),$post->title),$body);

?>
