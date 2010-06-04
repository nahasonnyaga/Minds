<?php
/**
 * Special upload form for
 */
$upload_sections = elgg_get_array_value('upload_sections', $vars, array());
$active_section = get_input('active_upload_section', array_shift(array_keys($upload_sections)));

$options = array();

if ($upload_sections) {
	foreach ($upload_sections as $id => $info) {
		$options[$id] = $info['name'];
	}

	$input = elgg_view('input/pulldown', array(
		'name' => 'download_section',
		'options_values' => $options,
		'internalid' => 'embed_upload',
		'value' => $active_section
	));

	echo "<p>$input</p>";

	if (!$upload_content = elgg_view($upload_sections[$active_section]['view'])) {
		$upload_content = elgg_echo('embed:no_upload_content');
	}

	echo $upload_content;

?>
	<script type="text/javascript">
	$(document).ready(function() {

		// change for pulldown
		$('#embed_upload').change(function() {
			var upload_section = $(this).val();
			var url = '<?php echo $vars['url']; ?>pg/embed/embed?active_section=upload&active_upload_section=' + upload_section;
			$('#facebox .body .content').load(url);
		});

	});
	</script>
<?php

} else {
	echo elgg_echo('embed:no_upload_sections');
}
