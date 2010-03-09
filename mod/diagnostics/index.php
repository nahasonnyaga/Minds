<?php
/**
 * Elgg diagnostics
 *
 * @package ElggDiagnostics
 * @author Curverider Ltd
 * @link http://elgg.com/
 */

require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

admin_gatekeeper();
set_context('admin');

// system diagnostics
$body = elgg_view_title(elgg_echo('diagnostics'));
$body .= "<div class='admin_settings diagnostics'>";
$body .= elgg_view('page_elements/elgg_content', array('body' =>
	"<h3>".elgg_echo('diagnostics:report')."</h3>".elgg_echo('diagnostics:description') . elgg_view('diagnostics/forms/download'))
);

// unit tests
$body .= "<h3>".elgg_echo('diagnostics:unittester')."</h3>";
$test_body = "<p>" . elgg_echo('diagnostics:unittester:description') . "</p>";
$test_body .= "<p>" . elgg_echo('diagnostics:unittester:warning') . "</p>";

if (isset($CONFIG->debug)) {
	// create a button to run tests
	$js = "onclick=\"window.location='{$CONFIG->wwwroot}engine/tests/suite.php'\"";
	$params = array('type' => 'button', 'value' => elgg_echo('diagnostics:test:executeall'), 'js' => $js);
	$test_body .= elgg_view('input/button', $params);
} else {
	// no tests when not in debug mode
	$test_body .= elgg_echo('diagnostics:unittester:debug');
}

$body .= elgg_view('page_elements/elgg_content', array(
	'body' => $test_body)
);
$body .= "</div>";
// create page
page_draw(elgg_echo('diagnostics'), elgg_view_layout("one_column_with_sidebar", $body));
