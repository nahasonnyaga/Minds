<?php
/**
 * Wall river views
 */
$item = $vars['item'];

if($item->body){
	$excerpt = minds_filter($item->body);
}else{
	$object = $item->getObjectEntity();
	$excerpt = minds_filter($object->message);
}

$to = get_entity($object->to_guid);

$subject = $item->getSubjectEntity();
$subject_link = elgg_view('output/url', array(
	'href' => $subject->getURL(),
	'text' => $subject->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
));

$owner_link = elgg_view('output/url', array(
	'href' => elgg_instanceof($to, 'group') ? "wall/group/$to->guid" : "wall/$to->username",
	'text' => $to->name,
	'class' => 'elgg-river-object',
	'is_trusted' => true,
));

if($object->owner_guid == $object->to_guid || $to instanceof ElggGroup || !$to){
	$summary = elgg_echo("river:create:object:wall", array($subject_link));
} else {
	$summary = elgg_echo("river:create:object:wall", array($subject_link, $owner_link));
}

if($item->attachment_guid){
	$attachment = elgg_view('output/img', array( 
		'src' => elgg_get_site_url() . "photos/thumbnail/$item->attachment_guid/large",
		'class' => 'river-img-attachment'
	)); //we are just going to assume they are images... change soon
}

echo elgg_view('river/elements/layout', array(
	'item' => $item,
	'message' => '<p>' . $excerpt . '</p>',
	'summary' => $summary,
	'attachments' => $attachment
));
