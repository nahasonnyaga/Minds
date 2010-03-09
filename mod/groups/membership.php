<?php

	/**
	 * Elgg groups 'member of' page
	 * 
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 */

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	gatekeeper();
	group_gatekeeper();
	
	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);
	
	if (page_owner() == $_SESSION['user']->guid) {
		$title = elgg_echo("groups:yours");
	} else $title = elgg_echo("groups:owned");

	// Get objects
	$area2 = elgg_view('page_elements/content_header', array('context' => "mine", 'type' => 'groups'));
	
	set_context('search');
	$objects = list_entities_from_relationship('member',page_owner(),false,'group','',0, $limit,false, false);
	set_context('groups');
	
	$area2 .= $objects;
	$body = elgg_view_layout('one_column_with_sidebar', $area1.$area2);
	
	// Finally draw the page
	page_draw($title, $body);
?>