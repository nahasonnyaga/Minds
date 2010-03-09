<?php
/**
 * Elgg bookmarks plugin everyone page
 * 
 * @package ElggBookmarks
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider <info@elgg.com>
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.org/
 */

// Start engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
		
// get the filter menu
$area1 = elgg_view('page_elements/content_header', array('context' => "everyone", 'type' => 'bookmarks'));
		
// List bookmarks
set_context('search');
$area2 .= list_entities('object','bookmarks');
set_context('bookmarks');

// if logged in, get the bookmarklet
if(isloggedin()){
	$area3 = elgg_view("bookmarks/bookmarklet");	
}	
//include a view for plugins to extend
$area3 .= elgg_view("bookmarks/sidebar_options", array("object_type" => 'bookmarks'));
//include stats
$area3 .= elgg_view("bookmarks/stats");
// Format page
$body = elgg_view_layout('one_column_with_sidebar', $area1.$area2, $area3);
		
// Draw it
echo page_draw(elgg_echo('bookmarks:all'),$body);