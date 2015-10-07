<?php

$notification = elgg_extract('entity', $vars);
$params = unserialize($notification->params);
$type = $params['type'] ? $params['type'] : 'entity';

$actor = get_entity($notification->from_guid, 'user');
$entity = \Minds\Core\Entities::build(new \Minds\Entities\entity($notification->object_guid));
try{

    if($entity->title)
        $object_title = $entity->title;
    else
        $object_title = "your post";

	$body .= elgg_view('output/url', array('href'=>$actor->getURL(), 'text'=>$actor->name));
	$body .= ' has voted up ';
	$body .= elgg_view('output/url', array('href'=>$entity->getURL(), 'text'=> $object_title));
	
	$body .= "<br/>";
	
	$body .= "<div class='notify_description'>" .  $description . "</div>";
	
	$body .= "<span class='notify_time'>" . elgg_view_friendly_time($notification->time_created) . "</span>";
	
	echo $body;
	
} catch(Exception $e){
	return false;
}
