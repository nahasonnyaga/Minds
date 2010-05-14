<?php
/**
 * Elgg index page for web-based applications
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

/**
 * Start the Elgg engine
 */
require_once(dirname(__FILE__) . "/engine/start.php");

if (!trigger_plugin_hook('index', 'system', null, FALSE)) {
	if (isloggedin()) {
		forward('pg/dashboard/');
	}

	/*
	River dashboard should respond to the index:system plugin hook instead of
	being hard-coded here.
	if(is_plugin_enabled('riverdashboard')){
		$title = elgg_view_title(elgg_echo('content:latest'));
		set_context('search');
		$content = elgg_list_registered_entities(array('limit' => 10, 'full_view' => FALSE, 'allowed_types' => array('object','group')));
		set_context('main');
	}
	*/
	
	//Load the front page
	$title = elgg_view_title(elgg_echo('content:latest'));
	set_context('search');
	$offset = (int)get_input('offset', 0);
	if(is_plugin_enabled('riverdashboard'))
		$activity = elgg_view_river_items(0, 0, '', '', '', '', 10, 0, 0, true, true);
	else
		$activity = elgg_list_registered_entities(array('limit' => 10, 'offset' => $offset, 'full_view' => FALSE, 'allowed_types' => array('object','group')));
	set_context('main');
	global $autofeed;
	$autofeed = FALSE;

	// if drop-down login in header option not selected
	$login_form = elgg_view('account/forms/login');
		
	$content = elgg_view_layout('one_column_with_sidebar', $title . $activity, $login_form);
	page_draw(null, $content);
}
