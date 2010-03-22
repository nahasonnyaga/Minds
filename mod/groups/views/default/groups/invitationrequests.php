<?php

if (!empty($vars['invitations']) && is_array($vars['invitations'])) {
	$user = get_loggedin_user();
	foreach($vars['invitations'] as $group)
		if ($group instanceof ElggGroup) {
		
		?>
		<div class="entity_listing group_invitations clearfloat">
			<?php
				echo "<div class='entity_listing_icon'>";
				echo elgg_view("profile/icon", array(
					'entity' => $group,
					'size' => 'tiny',
					'override' => 'true'
				))."</div>";

			$url = elgg_add_action_tokens_to_url("{$vars['url']}action/groups/join?user_guid={$user->guid}&group_guid={$group->guid}");
			?>
			<div class="entity_listing_info">
			<a href="<?php echo $url; ?>" class="submit_button"><?php echo elgg_echo('accept'); ?></a>
			<?php		
				echo str_replace('<a', '<a class="action_button disabled" ', elgg_view('output/confirmlink',array(
					'href' => $vars['url'] . "action/groups/killinvitation?user_guid={$user->getGUID()}&group_guid={$group->getGUID()}",
					'confirm' => elgg_echo('groups:invite:remove:check'),
					'text' => elgg_echo('delete'),
				)));
			
			echo "<p class='entity_title'><a href=\"" . $group->getUrl() . "\">" . $group->name . "</a></p>";
			echo "<p class='entity_subtext'>" . $group->briefdescription . "</p>";

			?>
		</div></div>
		<?php
		}

	} else {
		echo "<p class='margin_top'>" . elgg_echo('groups:invitations:none') . "</p>";
}
?>