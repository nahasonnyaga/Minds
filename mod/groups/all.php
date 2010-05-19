<?php
	/**
	 * Elgg groups plugin
	 *
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 */

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);
	$filter = get_input("filter");
	if (!$filter) {
		// active discussions is the default
		$filter = "active";
	}

	$context = get_context();

	switch($filter){
		case "newest":
			$objects = elgg_list_entities(array('types' => 'group', 'owner_guid' => 0, 'limit' => $limit, 'offset' => $offset, 'full_view' => false));
			break;

		case "pop":
			$objects = list_entities_by_relationship_count('member', true, "", "", 0, $limit, false);
			break;

		case "active":
		case 'default':
			$options = array(
				'type' => 'object',
				'subtype' => 'groupforumtopic',
				'annotation_name' => 'group_topic_post',
				'limit' => 40,
				'fullview' => TRUE
			);
			$objects = elgg_list_entities_from_annotations($options);
			break;
	}

	//get a group count
	$group_count = elgg_get_entities(array('types' => 'group', 'limit' => 10, 'count' => TRUE));

	//menu options
	$area2 = elgg_view("groups/side_menu");

	//find groups
	$area2 .= elgg_view("groups/find");

	//featured groups
	$featured_groups = elgg_get_entities_from_metadata(array('metadata_name' => 'featured_group', 'metadata_value' => 'yes', 'types' => 'group', 'limit' => 10));
	$area2 .= elgg_view("groups/featured", array("featured" => $featured_groups));

	set_context($context);

	$objects = "<div class='group_listings'>".$objects."</div>";

	$title = sprintf(elgg_echo("groups:all"), page_owner_entity()->name);
	$area1 .= elgg_view('page_elements/content_header', array('context' => "everyone", 'type' => 'groups', 'new_link' => "{$CONFIG->url}pg/groups/new"));
	$area1 .= elgg_view("groups/group_sort_menu", array("count" => $group_count, "filter" => $filter)) . $objects;
	$body = elgg_view_layout('one_column_with_sidebar', $area1, $area2);

	// Finally draw the page
	page_draw($title, $body);