<?php

if(!elgg_is_logged_in()){
	forward();
}

$node_guid = get_input('node_guid');
$node = new MindsNode($node_guid);

if(!$node){
        register_error('Node does not exists');
        forward();
}

$title = $node->launched ? $node->domain : 'New node';


$title_block = elgg_view_title($title, array('class' => 'elgg-heading-main'));
$buttons = elgg_view_menu('title', array(
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	));

$header = <<<HTML
<div class="elgg-head clearfix">
	$title_block$buttons
</div>
HTML;

$register_url = elgg_get_site_url() . 'action/node/edit';
$form_params = array(
	'action' => $register_url,
	'class' => 'elgg-form-account',
);

$body_params = array(
    'node' => $node
);
$content .= elgg_view_form('node', $form_params, $body_params);

$body = elgg_view_layout("content", array(	
					'header' => $header,
					'content'=> $content,
					'filter' => ''	
			));

echo elgg_view_page($title,$body); 
