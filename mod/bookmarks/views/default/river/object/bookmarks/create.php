<?php
/**
 * Elgg bookmark river entry view
 * 
 * @package ElggBookmarks
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider <info@elgg.com>
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.org/
 */

$performed_by = get_entity($vars['item']->subject_guid); // $statement->getSubject();
$object = get_entity($vars['item']->object_guid);
$url = $object->getURL();
$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
$string = "<div class=\"river_content_title\">" . sprintf(elgg_echo("bookmarks:river:created"),$url) . " ";
$string .= "<a href=\"" . $object->address . "\">" . $object->title . "</a> <span class=\"river_item_time\">" . friendly_time($object->time_updated) . "</span></div>"; //elgg_echo("bookmarks:river:item") . "</a></div>";
echo $string;