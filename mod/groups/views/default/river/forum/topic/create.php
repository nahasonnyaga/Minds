<?php

	$performed_by = get_entity($vars['item']->subject_guid); // $statement->getSubject();
	$object = get_entity($vars['item']->object_guid);
	$object_url = $object->getURL();
	$forumtopic = $object->guid;
	$group_guid = $object->container_guid;
	$url = $vars['url'] . "mod/groups/topicposts.php?topic=" . $forumtopic . "&group_guid=" . $group_guid;
	$comment = $object->getAnnotations("group_topic_post", 1, 0, "asc"); 
	foreach($comment as $c){
		$contents = $c->value;
	}
	$contents = strip_tags($contents);//this is so we don't get large images etc in the activity river
	$url_user = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
	$string = sprintf(elgg_echo("groupforum:river:postedtopic"),$url_user) . ": ";
	$string .= "<a href=\"" . $url . "\">" . $object->title . "</a> <span class='entity_subtext'>" . friendly_time($object->time_created) . "</span> <a class='river_comment_form_button link' href=\"{$object_url}\">Visit discussion</a>";
	$string .= elgg_view('likes/forms/link', array('entity' => $object));
	$string .= "<div class=\"river_content_display\">";
	$string .= elgg_make_excerpt($contents, 200);
	$string .= "</div>";
	
	echo $string;