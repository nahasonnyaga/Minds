<?php
/**
 * Elgg members index page
 * 
 * @package ElggMembers
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider <info@elgg.com>
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */

require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// Get the current page's owner
$page_owner = page_owner_entity();
if ($page_owner === false || is_null($page_owner)) {
	$page_owner = $_SESSION['user'];
	//set_page_owner($page_owner->getGUID());
}

// get filter parameters
$limit = get_input('limit', 10);
$offset = get_input('offset', 0);
$filter = get_input("filter", "newest");

// search options
$tag = get_input('tag');

// friends links
$area1 = "<div class='submenu page_navigation'>";
$area1 .= "<ul><li><a href=\"" . $CONFIG->wwwroot."pg/friends/" . page_owner_entity()->username . "\">". elgg_echo('friends') . "</a></li>";
$area1 .= "<li><a href=\"" . $CONFIG->wwwroot."pg/friendsof/" . page_owner_entity()->username . "\">". elgg_echo('friends:of') . "</a></li>";
$area1 .= "<li class='selected'><a href=\"" . $CONFIG->wwwroot."mod/members/index.php\">". elgg_echo('members:browse') . "</a></li>";
$area1 .= "</ul></div>";

//search members
$area1 .= elgg_view("members/search");

// count members
$members = get_number_users();

// title
$pagetitle = elgg_echo("members:members")." ({$members})";
$area2 = elgg_view_title($pagetitle);

//get the correct view based on filter
switch($filter){
	case "newest":
	$content = list_entities("user","",0,10,false);
	break;
	case "pop":
		$filter_content = list_entities_by_relationship_count('friend', true, '', '', 0, 10, false);
		break;
	case "active":
		$filter_content = get_online_users();
		break;
	// search based on name
	case "search":
		set_context('search');
		$filter_content = list_user_search($tag);
		break;
	// search based on tags
	case "search_tags":
		$filter_content = trigger_plugin_hook('search','',$tag,"");
		$filter_content .= list_entities_from_metadata("", $tag, "user", "", "", 10, false, false);
		break;
	case "newest":
	case 'default':
		$filter_content = elgg_list_entities(array('type' => 'user', 'offset' => $offset, 'full_view' => FALSE));
		break;
}

$area2 .= elgg_view('page_elements/elgg_content', array('body' => elgg_view("members/members_navigation", array("count" => $members, "filter" => $filter)) . "<div class='members_list'>".$filter_content."</div>", 'subclass' => 'members'));

//select the correct canvas area
$body = elgg_view_layout("one_column_with_sidebar", $area2, $area1);

// Display page
page_draw(sprintf(elgg_echo('members:members'), $page_owner->name), $body);