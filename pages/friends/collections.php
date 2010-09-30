<?php
/**
 * Elgg collections of friends
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

// You need to be logged in for this one
gatekeeper();

$title = elgg_echo('friends:collections');

$content = elgg_view_title($title);

$content .= elgg_view_access_collections(get_loggedin_userid());

$body = elgg_view_layout('one_column_with_sidebar', $content);

page_draw($title, $body);
