<?php
/**
 * TinyMCE CSS
 *
 * Overrides on the default TinyMCE skin
 * Gives the textarea and buttons rounded corners
 * 
 * @todo why the crazy long rules?
 */
?>
/* TinyMCE */
.elgg-page .mceEditor table.mceLayout {
	border: 1px solid #CCC;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
}
.elgg-page table.mceLayout tr.mceFirst td.mceToolbar,
.elgg-page table.mceLayout tr.mceLast td.mceStatusbar {
	border-width: 0px;
}
.mceButton {
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
