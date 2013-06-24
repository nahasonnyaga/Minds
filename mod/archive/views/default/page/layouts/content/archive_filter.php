<?php
/**
 * Main content filter
 *
 * Select between user, friends, and all content
 *
 * @uses $vars['filter_context']  Filter context: all, friends, mine
 * @uses $vars['filter_override'] HTML for overriding the default filter (override)
 * @uses $vars['context']         Page context (override)
 */


$context = elgg_extract('context', $vars, elgg_get_context());

if ($context) {
	$username = elgg_get_logged_in_user_entity()->username;
	$type_context = get_input('filter', 'all');
	$filter_context = elgg_extract('filter_context', $vars, 'all');
	
	$tabs = array(

		'all' => array(
			'text' => elgg_echo('all'),
                        'href' => "$context/all?filter=$type_context",
                        'selected' => ($filter_context == 'all'),
                        'priority' => 0,
                        'section'=>'filter'
		),		

		'friends' => array(
                        'text' => elgg_echo('friends'),
                        'href' => "$context/network?filter=$type_context",
                        'selected' => ($filter_context == 'network'),
                        'priority' => 0,
                        'section'=>'filter'
                ),

		'mine' => array(
			'text' => elgg_echo('mine'),
			'href' => (isset($vars['mine_link'])) ? $vars['mine_link'] : "$context/owner/$username?filter=$type_context",
			'selected' => ($filter_context == 'mine'),
			'priority' => 300,
			'section' => 'filter'
		),

		'filter:all' => array(
			'text' => elgg_echo('all'),
			'href' => '?filter=all',
			'selected' => ($type_context == 'all'),
			'priority' => 0,
			'section'=>'type'
		),
		'filter:media' => array(
			'text' => elgg_echo('kalturavideo:label:videoaudio'),
			'href' => '?filter=media',
			'selected' => ($type_context == 'media'),
			'priority' => 100,
			'section'=>'type'
		),
		'filter:images' => array(
			'text' => elgg_echo('photos'),
			'href' => '?filter=images',
			'selected' => ($type_context == 'images'),
			'priority' => 200,
			'section'=>'type'
		),
		'filter:files' => array(
			'text' => elgg_echo('file'),
			'href' => '?filter=files',
			'selected' => ($type_context == 'files'),
			'priority' => 300,
			'section'=>'type'
		),

	);
	
	if(!elgg_is_logged_in()){
		unset($tabs['mine']);
		unset($tabs['friends']);
	}
	
	foreach ($tabs as $name => $tab) {
		
		//remove other options if on the featured wall
				
		$tab['name'] = $name;
		
		elgg_register_menu_item('filter', $tab);
	}

	echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));
}
