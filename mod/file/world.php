<?php
	/**
	 * Elgg file browser
	 * 
	 * @package ElggFile
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2010 - 2009
	 * @link http://elgg.com/
	 */

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);
	$tag = get_input("tag");
	
	// Get the current page's owner
		$page_owner = page_owner_entity();
		if ($page_owner === false || is_null($page_owner)) {
			$page_owner = $_SESSION['user'];
			set_page_owner($_SESSION['guid']);
		}
	
	$title = elgg_echo('file:all');
	
	// Get objects
	$area1 = elgg_view('page_elements/content_header', array('context' => "everyone", 'type' => 'file'));
	$area1 .= get_filetype_cloud(); // the filter
	set_context('search');
	if ($tag != "")
		$area2 .= list_entities_from_metadata('tags',$tag,'object','file',0,10,false);
	else
		$area2 .= elgg_list_entities(array('types' => 'object', 'subtypes' => 'file', 'limit' => 10, 'offset' => $offset, 'full_view' => FALSE));
	set_context('file');

	//get the latest comments on all files
	$comments = get_annotations(0, "object", "file", "generic_comment", "", 0, 4, 0, "desc");
	$area3 = elgg_view('annotation/latest_comments', array('comments' => $comments));
	
	$content = "<div class='files'>".$area1.$area2."</div>";
		
	$body = elgg_view_layout('one_column_with_sidebar', $content, $area3);

	// Finally draw the page
	page_draw($title, $body);
?>