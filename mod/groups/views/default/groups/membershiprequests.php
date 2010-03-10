<?php

	if (!empty($vars['requests']) && is_array($vars['requests'])) {

		foreach($vars['requests'] as $request)
				if ($request instanceof ElggUser) {
	
	?>
		<div class="entity_listing group_invitations clearfloat">
				<?php
					echo "<div class='entity_listing_icon'>";
					echo elgg_view("profile/icon", array(
						'entity' => $request,
						'size' => 'small',
						'override' => 'true'
					));
					echo "</div>";
					
					$url = elgg_add_action_tokens_to_url("{$vars['url']}action/groups/addtogroup?user_guid={$request->guid}&group_guid={$vars['entity']->guid}");
					?>
					<div class="entity_listing_info">
					<a href="<?php echo $url; ?>" class="submit_button"><?php echo elgg_echo('accept'); ?></a>
					<?php	
					echo str_replace('<a', '<a class="action_button disabled" ', elgg_view('output/confirmlink',array(
						'href' => $vars['url'] . 'action/groups/killrequest?user_guid='.$request->guid.'&group_guid=' . $vars['entity']->guid,
						'confirm' => elgg_echo('groups:joinrequest:remove:check'),
						'text' => elgg_echo('delete'),
					)));
				echo "<p class='entity_title'><a href=\"" . $request->getUrl() . "\">" . $request->name . "</a></p>";
				echo "<p class='entity_subtext'>" . $request->briefdescription . "</p>";
				?>
			</div>
		</div>
	<?php
		}
	} else {
		echo "<p>" . elgg_echo('groups:requests:none') . "</p>";
	}

?>