<?php
/**
 * Action for adding a wire post
 * 
 */

// don't filter since we strip and filter escapes some characters
$body = get_input('body', '', false);

$method = 'site';
$to_guid = get_input('to_guid');
$from_guid = elgg_get_logged_in_user_guid();
$access_id = get_default_access(); //hard coded as we seem to be getting errors with ACCESS_DEFAULT
$message = get_input('body');
$ref = get_input('ref', 'wall');

// get social permissions
$facebook = get_input('facebook');
$twitter =  get_input('twitter');

// make sure the post isn't blank
if (empty($body)) {
	register_error(elgg_echo("wall:blank"));
	forward(REFERER);
}

$to = get_entity($to_guid, 'user');
if($to instanceof ElggGroup){
	$access_id = $to->group_acl;
	$container_guid = $to_guid;
}

//elgg_set_context('wall_post');

$post = new WallPost;
$post->to_guid = $to_guid;
$post->container_guid = $container_guid;
$post->owner_guid = $from_guid;
$post->access_id = $access_id;
$post->message = $message;
$post->method = $method;

$post->facebook = $facebook;
$post->twitter = $twitter;

$guid = $post->save();
if (!$guid) {
	register_error(elgg_echo("wall:error"));
	//forward(REFERER);
} else {

//add the message
$news_id = add_to_river('river/object/wall/create', 'create', $from_guid, $guid);

if($ref == 'wall'){
	$post = get_entity($guid,'object');

	$id = "elgg-{$post->getType()}-{$post->guid}";
	$time = $post->time_created;
	$output = "<li id=\"$id\" class=\"elgg-item\" data-timestamp=\"$time\">";
	$output .= elgg_view_list_item($post);
	$output .= '</li>';
} elseif($ref=='news'){

	$data = new stdClass();
	$data->id = $news_id;
	$data->action_type = 'create';
	$data->subject_guid = $from_guid;
	$data->object_guid = $guid;
	$data->view = 'river/object/wall/create';
	$data->posted = time();
	
	$item = new MindsNewsItem($data);

	$output = '<li class="elgg-item">' . elgg_view_list_item($item, array('list_class'=>'elgg-list elgg-list-river elgg-river', 'class'=>'elgg-item elgg-river-item')) . '</li>';
}

echo $output;

//notification_create(array($to_guid), $from_guid, $guid, array('description'=>$message,'notification_view'=>'wall'));

system_message(elgg_echo("wall:posted"));

}
