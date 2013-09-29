<?php
/**
 * Channels index
 *
 */

elgg_load_library('channels:suggested');

$page_owner = elgg_get_logged_in_user_entity();
elgg_set_page_owner_guid($page_owner->guid);

$friends = get_input('friends', 10);
$groups = get_input('groups', 10);

$num_members = get_number_users();

$limit = get_input('limit', 20);
$offset = get_input('offset', '');

$title = elgg_echo('channels');

$options = array('type' => 'user', 'full_view' => false, 'limit'=>$limit);
switch ($vars['page']) {
	case 'subscribers':
		$subscribers = get_user_friends_of($page_owner->guid, '', $limit, $offset);
		$content = elgg_view_entity_list($subscribers,$vars, $offset, $limit, false, false, true) . elgg_view('navigation/pagination', array('limit'=>$limit, 'offset'=>$offset,'count'=>1000));
		break;
	case 'subscriptions':
		$subscriptions = get_user_friends($page_owner->guid, '', $limit, $offset);
                $content = elgg_view_entity_list($subscriptions,$vars, $offset, $limit, false, false, true) . elgg_view('navigation/pagination', array('limit'=>$limit, 'offset'=>$offset,'count'=>1000));
		break;
	case 'popular':
		$options['limit'] = $limit;
		$options['relationship'] = 'friend';
		$options['inverse_relationship'] = false;
		$content = elgg_list_entities_from_relationship_count($options);
		break;
	/*case 'suggested':
		$people = suggested_friends_get_people($page_owner->guid, $friends, $groups);
		$entities = array();
		foreach($people as $person){
			$entities[] = $person['entity'];
		}
		$content = elgg_view_entity_list($entities);
		break;
	case 'online':
		$content = get_online_users($limit);
		break;
	case 'collections':
		elgg_register_title_button('collections', 'add');
		$content = elgg_view_access_collections(elgg_get_logged_in_user_guid());
		break;*/
	case 'newest':
	default:
		$content = elgg_list_entities($options);
		break;
}

$params = array(
	'content' => $content,
	'sidebar' => elgg_view('channels/sidebar'),
	'title' => $title,
	'filter_override' => elgg_view('channels/nav', array('selected' => $vars['page'])),
	'class'=> 'channels'
);

$body = elgg_view_layout('tiles', $params);

echo elgg_view_page($title, $body);
