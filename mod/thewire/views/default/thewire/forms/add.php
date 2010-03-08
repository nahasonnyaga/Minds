<?php

	/**
	 * Elgg thewire edit/add page
	 * 
	 * @package ElggTheWire
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 * 
	 */

		$wire_user = get_input('wire_username');
		if (!empty($wire_user)) { $msg = '@' . $wire_user . ' '; } else { $msg = ''; }

?>
<div class="new_wire_post clearfloat">
<h3><?php echo elgg_echo("thewire:doing"); ?></h3>
<script type="text/javascript" src="<?php echo $vars['url']; ?>mod/thewire/views/default/thewire/scripts/counter.js"></script>
<form action="<?php echo $vars['url']; ?>action/thewire/add" method="post" name="new_post">
	<?php
		$action_txt = elgg_echo('post');
	    $display .= "<textarea name='new_post_textarea' value='' onKeyDown=\"textCounter(document.new_post.new_post_textarea,document.new_post.remLen1,140)\" onKeyUp=\"textCounter(document.new_post.new_post_textarea,document.new_post.remLen1,140)\">{$msg}</textarea>";
        $display .= "<input type='submit' class='action_button' value='{$action_txt}' />";
        $display .= "<div class='character_count'><input readonly type=\"text\" name=\"remLen1\" size=\"3\" maxlength=\"3\" value=\"140\">";
        echo $display;
        echo elgg_echo("thewire:charleft") . "</div>";
		echo elgg_view('input/securitytoken');
	?>
	<input type="hidden" name="method" value="site" />
</form>
</div>
<?php echo elgg_view('input/urlshortener'); ?>