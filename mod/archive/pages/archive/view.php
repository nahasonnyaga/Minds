<?php

$guid = (int) get_input('guid');

$entity = get_entity($guid);

elgg_set_page_owner_guid($entity->getOwnerGUID());
$owner = elgg_get_page_owner_entity();

$title = $entity->title;
$description = strip_tags($videopost->description);

if($entity->getSubtype() == 'kaltura_video'){
	
	elgg_load_library('archive:kaltura');
	
	//set the tags
	$kaltura_server = elgg_get_plugin_setting('kaltura_server_url',  'archive');
	$partnerId = elgg_get_plugin_setting('partner_id', 'archive');
	
	$widgetUi = elgg_get_plugin_setting('custom_kdp', 'kaltura_video');
	
	$video_location = $kaltura_server . '/index.php/kwidget/wid/_'.$partnerId.'/uiconf_id/' . $widgetUi . '/entry_id/'. $entity->kaltura_video_id;
	$video_location_secure = str_replace('http://', 'https://', $video_location);	
	
	$thumbnail = kaltura_get_thumnail($entity->kaltura_video_id, 640, 360, 100);	
	
	minds_set_metatags('og:type', 'video.other');
	//minds_set_metatags('og:url',trim($videopost->getURL()));
	minds_set_metatags('og:image', $thumbnail);
	minds_set_metatags('og:title', $title);
	minds_set_metatags('og:description', $description);
	minds_set_metatags('og:video', $video_location);
	minds_set_metatags('og:video:secure_url',  $video_location_secure); 
	minds_set_metatags('og:video:width', '1280');
	minds_set_metatags('og:video:height', '720');
	minds_set_metatags('og:other', $video_location);
	 
	minds_set_metatags('twitter:card', 'player');
	minds_set_metatags('twitter:url', $entity->getURL());
	minds_set_metatags('twitter:title', $entity->title);
	minds_set_metatags('twitter:image', $thumbnail);
	minds_set_metatags('twitter:description', $description);
	minds_set_metatags('twitter:player', $video_location);
	minds_set_metatags('twitter:player:width', '1280');
	minds_set_metatags('twitter:player:height', '720');
	
} elseif($entity->getSubtype() == 'file'){
	
	minds_set_metatags('og:type', 'article');
	minds_set_metatags('og:url', $entity->getURL());
	minds_set_metatags('og:image', $entity->getIconURL('large'));
	minds_set_metatags('og:title', $title);
	minds_set_metatags('og:description', $description);
	
	 
	minds_set_metatags('twitter:card', 'summary');
	minds_set_metatags('twitter:url', $entity->getURL());
	minds_set_metatags('twitter:title', $title);
	minds_set_metatags('twitter:image', $entity->getIconURL());
	minds_set_metatags('twitter:description', $description);
	
}

elgg_push_breadcrumb(elgg_echo('archive:all'), 'archive/all');

$crumbs_title = $owner->name;
if (elgg_instanceof($owner, 'group')) {
	elgg_push_breadcrumb($crumbs_title, "archive/group/$owner->guid/all");
} else {
	elgg_push_breadcrumb($crumbs_title, "archive/$owner->username");
}

elgg_push_breadcrumb($title);

$content = elgg_view_entity($entity, array('full_view' => true));
$content .= elgg_view('minds/ads', array('type'=>'content-foot'));
$content .= elgg_view_comments($entity);

$sidebar = elgg_view('archive/sidebar', array('guid'=>$guid));

$body = elgg_view_layout("content", array(	
					'filter'=> '', 
					'title' => $title,
					'content'=> $content,
					'sidebar' => $sidebar 
				));

echo elgg_view_page($title,$body);

?>
