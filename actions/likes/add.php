<?php
/**
 * Elgg add like action
 *
 * @package Elgg
 * @author Curverider <curverider.co.uk>
 * @link http://elgg.org/
 */

gatekeeper();
$entity_guid = (int) get_input('guid');

//check to see if the user has already liked the item
if (elgg_annotation_exists($entity_guid, 'likes')){
	system_message(elgg_echo("likes:alreadyliked"));
	forward($_SERVER['HTTP_REFERER']);
}
// Let's see if we can get an entity with the specified GUID
$entity = get_entity($entity_guid);
if (!$entity) {
	register_error(elgg_echo("likes:notfound"));
	forward($_SERVER['HTTP_REFERER']);
}

$user = get_loggedin_user();
$annotation = create_annotation($entity->guid,
								'likes',
								"likes",
								"",
								$user->guid,
								$entity->access_id);

// tell user annotation didn't work if that is the case
if (!$annotation) {
	register_error(elgg_echo("likes:failure"));
	forward($_SERVER['HTTP_REFERER']);
}

// notify if poster wasn't owner
if ($entity->owner_guid != $user->guid) {

	notify_user($entity->owner_guid,
				$user->guid,
				elgg_echo('likes:email:subject'),
				sprintf(
					elgg_echo('likes:email:body'),
					$entity->title,
					$user->name,
					$comment_text,
					$entity->getURL(),
					$user->name,
					$user->getURL()
				)
			);
}

system_message(elgg_echo("likes:likes"));
//add to river
add_to_river('annotation/annotatelike','likes',$user->guid,$entity->guid, "", 0, $annotation);

// Forward back to the page where the user 'liked' the object
forward($_SERVER['HTTP_REFERER']);
