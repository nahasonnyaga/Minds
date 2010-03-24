<?php
/**
 * Elgg generic comment
 */

$owner = get_user($vars['annotation']->owner_guid);

?>
<a name="comment_<?php echo $vars['annotation']->id; ?>"></a>
<div class="generic_comment clearfloat">
	<div class="generic_comment_icon">
		<?php
			echo elgg_view("profile/icon", array(
					'entity' => $owner,
					'size' => 'tiny'
					));
		?>
	</div>

	<div class="generic_comment_details">
		<?php
		// if the user looking at the comment can edit, show the delete link
		if ($vars['annotation']->canEdit()) {
		?>
			<span class="delete_button">
				<?php echo elgg_view("output/confirmlink",array(
						'href' => $vars['url'] . "action/comments/delete?annotation_id=" . $vars['annotation']->id,
						'text' => elgg_echo('delete'),
						'confirm' => elgg_echo('deleteconfirm')
						));
				?>
			</span>
		<?php
			} //end of can edit if statement
		?>
		<p class="generic_comment_owner">
			<a href="<?php echo $owner->getURL(); ?>"><?php echo $owner->name; ?></a>
			<span class="entity_subtext">
				<?php echo friendly_time($vars['annotation']->time_created); ?>
			</span>
		</p>
		<!-- output the actual comment -->
		<div class="generic_comment_body">
			<?php echo elgg_view("output/longtext",array("value" => $vars['annotation']->value)); ?>
		</div>
	</div>
</div>