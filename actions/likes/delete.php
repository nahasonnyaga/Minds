<?php
/**
 * Elgg delete like action
 *
 * @package Elgg
 * @author Curverider <curverider.co.uk>
 * @link http://elgg.org/
 */

// Ensure we're logged in
if (!isloggedin()) {
	forward();
}

// Make sure we can get the comment in question
$annotation_id = (int) get_input('annotation_id');
if ($likes = get_annotation($annotation_id)) {

	$entity = get_entity($likes->entity_guid);

	if ($likes->canEdit()) {
		$likes->delete();
		system_message(elgg_echo("likes:deleted"));
		forward($entity->getURL());
	}

} else {
	$url = "";
}

register_error(elgg_echo("likes:notdeleted"));
forward($_SERVER['HTTP_REFERER']);