<?php

	$performed_by = get_entity($vars['item']->subject_guid); // $statement->getSubject();
	$object = get_entity($vars['item']->object_guid);
	$objecturl = $object->getURL();
	
	$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
	$string = sprintf(elgg_echo("groups:river:created"),$url) . " ";
	$string .= " <a href=\"" . $object->getURL() . "\">" . $object->name . "</a>";
	$string .= " <span class='entity_subtext'>". friendly_time($object->time_created) ."</span> ";
	if (isloggedin()) {
		$string .= elgg_view('likes/forms/link', array('entity' => $object));
	}

echo $string;