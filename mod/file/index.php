<?php
	/**
	 * Elgg file browser
	 * 
	 * @package ElggFile
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 * 
	 * 
	 * TODO: File icons, download & mime types
	 */

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

	// access check for closed groups
	group_gatekeeper();
	
	//set the title
	if (page_owner() == get_loggedin_userid()) {
		$title = elgg_echo('file:yours');
		$area1 = elgg_view('page_elements/content_header', array('context' => "mine", 'type' => 'file'));
	} else {
		$title = sprintf(elgg_echo("file:user"),page_owner_entity()->name);
		$area1 = elgg_view('page_elements/content_header', array('context' => "friends", 'type' => 'file'));
	}
		
	// Get objects
	set_context('search');
	$offset = (int)get_input('offset', 0);
	$area2 .= elgg_list_entities(array('types' => 'object', 'subtypes' => 'file', 'container_guid' => page_owner(), 'limit' => 10, 'offset' => $offset, 'full_view' => FALSE));
	set_context('file');
	$get_filter = get_filetype_cloud(page_owner());
	if ($get_filter) {
		$area1 .= $get_filter;
	} else {
		$area2 .= "<p class='margin_top'>".elgg_echo("file:none")."</p>";
	}
	
	//get the latest comments on the current users files
	$comments = get_annotations(0, "object", "file", "generic_comment", "", 0, 4, 0, "desc",0,0,page_owner());
	$area3 = elgg_view('annotation/latest_comments', array('comments' => $comments));
	
	$content = "<div class='files'>".$area1.$area2."</div>";
	$body = elgg_view_layout('one_column_with_sidebar', $content, $area3);
	
	page_draw($title, $body);
?>