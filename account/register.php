<?php

/**
 * Elgg registration page
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

/**
 * Start the Elgg engine
 *
 * WHY???? In the case this file is called thru a page handler: $CONFIG
 * is not within the global scope (the page handler function does not include it).
 * BUT, there _might_ exist direct calls to this file, requiring the engine
 * to be started. Logic for both cases follow.
 */
require_once(dirname(dirname(__FILE__)) . "/engine/start.php");
global $CONFIG;

// check new registration allowed
if (!$CONFIG->allow_registration) {
	register_error(elgg_echo('registerdisabled'));
	forward();
}

$friend_guid = (int) get_input('friend_guid',0);
$invitecode = get_input('invitecode');

// If we're not logged in, display the registration page
if (!isloggedin()) {
	$area1 = elgg_view_title(elgg_echo("register"));
	$area2 = elgg_view("account/forms/register", array('friend_guid' => $friend_guid, 'invitecode' => $invitecode));
	page_draw(elgg_echo("register"), elgg_view_layout("one_column_with_sidebar", $area1 . $area2));

// Otherwise, forward to the index page
} else {
	forward();
}
