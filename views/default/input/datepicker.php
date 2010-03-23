<?php
/**
 * Elgg datepicker input
 * Displays a text field with a popup date picker.
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 *
 * @uses $vars['value'] The current value, if any
 * @uses $vars['js'] Any Javascript to enter into the input tag
 * @uses $vars['internalname'] The name of the input field
 *
 */

$cal_name = sanitise_string($vars['internalname']);

if (isset($vars['class'])) {
	$class = "{$vars['class']} popup_calendar";
} else {
	$class = 'popup_calendar';
}

if (!isset($vars['value']) || $vars['value'] === FALSE) {
	$vars['value'] = elgg_get_sticky_value($vars['internalname']);
}

if ($vars['value'] > 86400) {
	//$val = date("F j, Y", $vars['value']);
	$val = date('n/d/Y', $vars['value']);
} else {
	$val = $vars['value'];
}

?>
<input type="text" name="<?php echo $vars['internalname']; ?>" value="<?php echo $val; ?>" />
<script language="JavaScript">
	$(document).ready(function() {
		$('input[type=text][name=<?php echo $cal_name; ?>]').datepicker();
	});
</script>
