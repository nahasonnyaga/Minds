<?php
/**
 * Groups latest activity
 *
 * @package Groups
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.org/
 */
?>

<h3><?php echo elgg_echo("activity"); ?></h3>
<?php
	$owner = page_owner_entity();
	$group_guid = $owner->guid;
	$limit = 5;

	$offset = (int) get_input('offset', 0);

	// Sanitise variables -- future proof in case they get sourced elsewhere
	$limit = (int) $limit;
	$offset = (int) $offset;
	$group_guid = (int) $group_guid;

	$sql = "SELECT {$CONFIG->dbprefix}river.id, {$CONFIG->dbprefix}river.type, {$CONFIG->dbprefix}river.subtype, {$CONFIG->dbprefix}river.action_type, {$CONFIG->dbprefix}river.access_id, {$CONFIG->dbprefix}river.view, {$CONFIG->dbprefix}river.subject_guid, {$CONFIG->dbprefix}river.object_guid, {$CONFIG->dbprefix}river.posted FROM {$CONFIG->dbprefix}river INNER JOIN {$CONFIG->dbprefix}entities AS entities1 ON {$CONFIG->dbprefix}river.object_guid = entities1.guid INNER JOIN {$CONFIG->dbprefix}entities AS entities2 ON entities1.container_guid = entities2.guid WHERE entities2.guid = $group_guid OR {$CONFIG->dbprefix}river.object_guid = $group_guid ORDER BY posted DESC limit {$offset},{$limit}";

	$items = get_data($sql);

    if (count($items) > 0) {
		$river_items = elgg_view('river/item/list',array(
								'limit' => $limit,
								'offset' => $offset,
								'items' => $items
								));
	}
	echo $river_items;

?>
