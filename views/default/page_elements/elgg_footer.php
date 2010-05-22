<?php
/**
 * Elgg footer
 * The standard HTML footer that displays across the site
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 *
 */

?>
<div id="elgg_footer">
	<div id="elgg_footer_contents" class="clearfloat">
		<?php echo elgg_view('footer/links'); ?>
		<a href="http://www.elgg.org" class="powered_by_elgg_badge">
			<img src="<?php echo $vars['url']; ?>_graphics/powered_by_elgg_badge_drk_bckgnd.gif" alt="Powered by Elgg" />
		</a>
	</div>
</div>

<?php echo elgg_view('footer/analytics'); ?>
