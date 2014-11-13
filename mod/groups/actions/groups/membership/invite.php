<?php
/**
 * Invite users to join a group
 *
 * @package ElggGroups
 */

$logged_in_user = elgg_get_logged_in_user_entity();

$username = get_input('username');

$group_guid = get_input('group_guid');
$group = get_entity($group_guid,'group');
$user = new minds\entities\user($username);
if($user->guid){
	if ($user && $group && ($group instanceof ElggGroup) && $group->canEdit()) {

		if (!check_entity_relationship($group->guid, 'invited', $user->guid)) {

			// Create relationship
			$result = add_entity_relationship($group->guid, 'invited', $user->guid);

			// Send email
			$url = elgg_normalize_url("groups/invitations/$user->username");
			\elgg_trigger_plugin_hook('notification', 'all', array(
				'to' => array($user->getGUID()),
				'object_guid'=>$group->guid,
				'invite_url' => $url,
				'notification_view'=>'group_invite'
			));
			
			if ($result) {
				system_message(elgg_echo("groups:userinvited"));
			} else {
				register_error(elgg_echo("groups:usernotinvited"));
			}
		} else {
			register_error(elgg_echo("groups:useralreadyinvited"));
		}
	}
}

forward(REFERER);
