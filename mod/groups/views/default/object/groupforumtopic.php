<?php

	/**
	 * Elgg Groups latest discussion listing
	 * 
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 */
	 
    //get the required variables
    $title = htmlentities($vars['entity']->title, ENT_QUOTES, 'UTF-8');
    //$description = get_entity($vars['entity']->description);
    $topic_owner = get_user($vars['entity']->owner_guid);
    $group = get_entity($vars['entity']->container_guid);
    $forum_created = friendly_time($vars['entity']->time_created);
    $counter = $vars['entity']->countAnnotations("group_topic_post");
	$last_post = $vars['entity']->getAnnotations("group_topic_post", 1, 0, "desc");
 
    //get the time and user
    if ($last_post) {
		foreach($last_post as $last) {
			$last_time = $last->time_created;
			$last_user = $last->owner_guid;
		}
	}

	$u = get_user($last_user);

	//select the correct output depending on where you are
	if(get_context() == "search"){
	
	    $info = "<p class='entity_subtext groups'>" . sprintf(elgg_echo('group:created'), $forum_created, $counter) .  "<br />";
	    if (($last_time) && ($u)) $info.= sprintf(elgg_echo('groups:lastupdated'), friendly_time($last_time), " <a href=\"" . $u->getURL() . "\">" . $u->name . "</a>");
	    $info .= '</p>';
		//get the group avatar
		$icon = elgg_view("profile/icon",array('entity' => $u, 'size' => 'tiny'));
	    //get the group and topic title
	    $info .= "<p class='entity_subtext'><b>" . elgg_echo('Topic') . ":</b> <a href=\"{$vars['url']}mod/groups/topicposts.php?topic={$vars['entity']->guid}&group_guid={$group->guid}\">{$title}</a></p>";
	    if ($group instanceof ElggGroup) {
	    	$info .= "<p class='entity_title'><b>" . elgg_echo('group') . ":</b> <a href=\"{$group->getURL()}\">".htmlentities($group->name, ENT_QUOTES, 'UTF-8') ."</a></p>";
	    }
		
	}else{
		
		$info = "<p class='entity_subtext groups'>" . sprintf(elgg_echo('group:created'), $forum_created, $counter) . "</p>";
	    $info .= "<p class='entity_title'>" . elgg_echo('groups:started') . " " . $topic_owner->name . ": <a href=\"{$vars['url']}mod/groups/topicposts.php?topic={$vars['entity']->guid}&group_guid={$group->guid}\">{$title}</a></p>";

	    if (groups_can_edit_discussion($vars['entity'], page_owner_entity()->owner_guid)) {
                	// display the delete link to those allowed to delete
                	$info .= "<div class='delete_button'>" . elgg_view("output/confirmlink", array(
                																'href' => $vars['url'] . "action/groups/deletetopic?topic=" . $vars['entity']->guid . "&group=" . $vars['entity']->container_guid,
                																'text' => " ",
                																'confirm' => elgg_echo('deleteconfirm'),
                															)) . "</div>";
                				
           }		

		if (($last_time) && ($u)) {
			$info.= "<p class='entity_subtext'>" . elgg_echo('groups:updated') . " " . friendly_time($last_time) . " by <a href=\"" . $u->getURL() . "\">" . $u->name . "</a></p>";		
		}
	    //get the user avatar
		$icon = elgg_view("profile/icon",array('entity' => $topic_owner, 'size' => 'tiny'));		
	}
		
		//display
		echo elgg_view_listing($icon, $info);
		
?>