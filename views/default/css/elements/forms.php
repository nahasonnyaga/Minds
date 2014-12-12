<?php
/**
 * CSS form/input elements
 *
 * @package Elgg.Core
 * @subpackage UI
 */
?>

/* ***************************************
	Form Elements
*************************************** */

.elgg-body .elgg-form{
	padding:12px;
}

fieldset > div {
	margin-bottom: 15px;
}
fieldset > div:last-child {
	margin-bottom: 0;
}
.elgg-form-alt > fieldset > .elgg-foot {
	border-top: 1px solid #CCC;
	padding: 10px 0;
}

label {
	font-weight: bold;
	color: #333;
	font-size: 110%;
}

input, textarea {
	border: 1px solid #eee;
	color: #666;
	padding: 8px;
	width: 100%;	
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

input[type=text]:focus, input[type=password]:focus, textarea:focus {
	border: solid 1px #CCC;
	background: #EEE;
	color:#333;
}

textarea {
	/*height: 200px;*/
}


.elgg-longtext-control {
	float: right;
	margin-left: 14px;
	font-size: 80%;
	cursor: pointer;
}


.elgg-input-access {
	margin:5px 0 0 0;
}

input[type="checkbox"],
input[type="radio"] {
	margin:0 3px 0 0;
	padding:0;
	border:none;
	width:auto;
}
.elgg-input-checkboxes.elgg-horizontal li,
.elgg-input-radios.elgg-horizontal li {
	display: inline;
	padding-right: 10px;
}

.elgg-form-login{
	width:400px;
	padding:32px;
	margin:auto;
	background:#FFF;
	border:1px solid #EEE;
	border-radius:6px;
}
.elgg-form-login .elgg-input-text, .elgg-form-login .elgg-input-password{
	margin:8px 0 ;
	padding:16px;
	font-size:12px;
	border:1px solid #DDD;
}
.elgg-form-login .elgg-menu-general{
	float:right;
	margin-top:4px;
} 
.elgg-form-login .elgg-menu-general li{
	margin: 8px
}

.node-select{
	
}

.node-select .label{
	float:left;
	background:#EEE;
	padding:16px 28px;
	border:1px solid #DDD;
	border-radius:3px 0 0 3px;
}
.node-select select, .node-select .select2-container > a{
	float:left;
	-webkit-appearance: none;  /*Removes default chrome and safari style*/
	-moz-appearance: none; /* Removes Default Firefox style*/
	width: 200px; /*Width of select dropdown to give space for arrow image*/
	
	color: #888;
	background:#FFF;
	
	padding:12px;
	border:1px solid #DDD;
	border-left:0;
	border-radius:0 3px 3px 0;
}

.elgg-form-account {
	margin:auto;
	padding:32px;
	border:1px solid #EEE;
	border-radius:2px;
	box-shadow:0 0 1px #AAA;
	-moz-box-shadow:0 0 1px #AAA;
	-webkit-box-shadow:0 0 1px #AAA;
	background:#FFF;
	width:70%;
	max-width:400px;
}

/* ***************************************
	FRIENDS PICKER
*************************************** */
.friends-picker-main-wrapper {
	margin-bottom: 15px;
}
.friends-picker-container h3 {
	font-size:4em !important;
	text-align: left;
	margin:10px 0 20px !important;
	color:#999 !important;
	background: none !important;
	padding:0 !important;
}
.friends-picker .friends-picker-container .panel ul {
	text-align: left;
	margin: 0;
	padding:0;
}
.friends-picker-wrapper {
	margin: 0;
	padding:0;
	position: relative;
	width: 100%;
}
.friends-picker {
	position: relative;
	overflow: hidden;
	margin: 0;
	padding:0;
	width: 730px;
	height: auto;
	background-color: #dedede;
	
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
}
.friendspicker-savebuttons {
	background: white;
	
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
	
	margin:0 10px 10px;
}
.friends-picker .friends-picker-container { /* long container used to house end-to-end panels. Width is calculated in JS  */
	position: relative;
	left: 0;
	top: 0;
	width: 100%;
	list-style-type: none;
}
.friends-picker .friends-picker-container .panel {
	float:left;
	height: 100%;
	position: relative;
	width: 730px;
	margin: 0;
	padding:0;
}
.friends-picker .friends-picker-container .panel .wrapper {
	margin: 0;
	padding:4px 10px 10px 10px;
	min-height: 230px;
}
.friends-picker-navigation {
	margin: 0 0 10px;
	padding:0 0 10px;
	border-bottom:1px solid #ccc;
}
.friends-picker-navigation ul {
	list-style: none;
	padding-left: 0;
}
.friends-picker-navigation ul li {
	float: left;
	margin:0;
	background:white;
}
.friends-picker-navigation a {
	font-weight: bold;
	text-align: center;
	background: white;
	color: #999;
	text-decoration: none;
	display: block;
	padding: 0;
	width:20px;
	
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}
.tabHasContent {
	background: white;
	color:#333 !important;
}
.friends-picker-navigation li a:hover {
	background: #333;
	color:white !important;
}
.friends-picker-navigation li a.current {
	background: #999;
	color:white !important;
}
.friends-picker-navigation-l, .friends-picker-navigation-r {
	position: absolute;
	top: 46px;
	text-indent: -9000em;
}
.friends-picker-navigation-l a, .friends-picker-navigation-r a {
	display: block;
	height: 40px;
	width: 40px;
}
.friends-picker-navigation-l {
	right: 48px;
	z-index:1;
}
.friends-picker-navigation-r {
	right: 0;
	z-index:1;
}
.friends-picker-navigation-l {
	background: url("<?php echo elgg_get_site_url(); ?>_graphics/friendspicker.png") no-repeat left top;
}
.friends-picker-navigation-r {
	background: url("<?php echo elgg_get_site_url(); ?>_graphics/friendspicker.png") no-repeat -60px top;
}
.friends-picker-navigation-l:hover {
	background: url("<?php echo elgg_get_site_url(); ?>_graphics/friendspicker.png") no-repeat left -44px;
}
.friends-picker-navigation-r:hover {
	background: url("<?php echo elgg_get_site_url(); ?>_graphics/friendspicker.png") no-repeat -60px -44px;
}
.friendspicker-savebuttons .elgg-button-submit,
.friendspicker-savebuttons .elgg-button-cancel {
	margin:5px 20px 5px 5px;
}
.friendspicker-members-table {
	background: #dedede;
	
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
	
	margin:10px 0 0;
	padding:10px 10px 0;
}

/* ***************************************
	AUTOCOMPLETE
*************************************** */
<?php //autocomplete will expand to fullscreen without max-width ?>
.ui-autocomplete {
	position: absolute;
	cursor: default;
}
.elgg-autocomplete-item .elgg-body {
	max-width: 600px;
}
.ui-autocomplete {
	background-color: white;
	border: 1px solid #ccc;
	overflow: hidden;

	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
.ui-autocomplete .ui-menu-item {
	padding: 0px 4px;
	
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
.ui-autocomplete .ui-menu-item:hover {
	background-color: #eee;
}
.ui-autocomplete a:hover {
	text-decoration: none;
	color: #4690D6;
}

/* ***************************************
	USER PICKER
*************************************** */
.elgg-user-picker-list li:first-child {
	border-top: 1px dotted #ccc;
	margin-top: 5px;
}
.elgg-user-picker-list > li {
	border-bottom: 1px dotted #ccc;
}

/* ***************************************
      DATE PICKER
**************************************** */
.ui-datepicker {
	display: none;

	margin-top: 3px;
	width: 208px;
	background-color: white;
	border: 1px solid #0054A7;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	overflow: hidden;

	-webkit-box-shadow: 4px 4px 4px rgba(0, 0, 0, 0.5);
	-moz-box-shadow: 4px 4px 4px rgba(0, 0, 0, 0.5);
	box-shadow: 4px 4px 4px rgba(0, 0, 0, 0.5);
}
.ui-datepicker-inline {
	-webkit-box-shadow: none;
	-moz-box-shadow: none;
	box-shadow: none;
}

.ui-datepicker-header {
	position: relative;
	background: #4690D6;
	color: white;
	padding: 2px 0;
	border-bottom: 1px solid #0054A7;
}
.ui-datepicker-header a {
	color: white;
}
.ui-datepicker-prev, .ui-datepicker-next {
    position: absolute;
    top: 5px;
	cursor: pointer;
}
.ui-datepicker-prev {
    left: 6px;
}
.ui-datepicker-next {
    right: 6px;
}
.ui-datepicker-title {
    line-height: 1.8em;
    margin: 0 30px;
    text-align: center;
	font-weight: bold;
}
.ui-datepicker-calendar {
	margin: 4px;
}
.ui-datepicker th {
	color: #0054A7;
	border: none;
    font-weight: bold;
    padding: 5px 6px;
    text-align: center;
}
.ui-datepicker td {
	padding: 1px;
}
.ui-datepicker td span, .ui-datepicker td a {
    display: block;
    padding: 2px;
	line-height: 1.2em;
    text-align: right;
    text-decoration: none;
}
.ui-datepicker-calendar .ui-state-default {
	border: 1px solid #ccc;
    color: #4690D6;;
	background: #fafafa;
}
.ui-datepicker-calendar .ui-state-hover {
	border: 1px solid #aaa;
    color: #0054A7;
	background: #eee;
}
.ui-datepicker-calendar .ui-state-active,
.ui-datepicker-calendar .ui-state-active.ui-state-hover {
	font-weight: bold;
    border: 1px solid #0054A7;
    color: #0054A7;
	background: #E4ECF5;
}

/**
 * Contact form
 */

.elgg-form-contact{
	width:600px;
	height:auto;
	display:block;
	margin:auto;
}
.elgg-form-contact input[type=text]{
	padding:16px;
}
.elgg-form-contact input.time{
	display:none;
}

/**
 * Search 
 */
/**
 * Minds Search
 */
.minds-search{}
form.minds-search{
	float:left;
	width:50%;
}
.minds-search input[type=text]{
	margin:10px;
}
.minds-search .submit{
	display:none;
}

.autocomplete-suggestion{
	background:#FFF;
	border:1px solid #DDD;
	padding:8px;
	cursor:pointer;
}
.autocomplete-suggestion:hover{
	background:#EEE;
}
.autocomplete-suggestion span.subtype{
	font-weight:bold;
	font-size:10px;
	text-align:right;
	float:right;
	color:#CCC;
}
.autocomplete-suggestion img{
	float: left;
	margin: 5px 8px 5px 0px;
}
.autocomplete-suggestion .subtitle{
	font-size:11px;
	color:#CCC;
}


/**
 * minds posting
 */
.elgg-form-deck-river-post,  .elgg-form-activity-post{
padding: 12px;
background: #F1F1F1;
border: 1px solid #CCC;
margin: 0;
width: 96%;
}
.elgg-form-activity-post textarea{
	border-radius:0;
	border:1px solid #DDD;
	height:34px;
}
.elgg-form-activity-post textarea:focus{
 	background:#FFF;
}

/**
 * 	Attacher
 */

.post-attachment-button-override{
	font-family:"fontello";
	background:#EEE;
	padding:0;
	border:1px solid #DDD;
	border-radius:0;
	color:#888;
	content: "\1f4ce"; /* \1f4ce */
	font-size:22px;
	position:relative;
	float:left;
	margin:2px;
	cursor:pointer;
	height:29px;
	width:36px;
}
.post-attachment-button-override:before{
	font-family: "fontello";
	color: #888;
	content: "\1f4ce";
	position: absolute;
	top: 7px;
	left: 6px;
}
.post-attachment-button-override.attached:before{
	color:#4690D6;
}
.post-attachment-button{
	position:absolute;
	font-family:"fontello";
	color:#888;
	content: "\1f4f7"; /* \1f4f7 */
	position: absolute;
	height: 100%;
	width:100%;
	top: 0;
	left: 0;
	cursor: pointer;
	opacity: 0;
	filter:alpha(opacity=0);
}
.post-post-preview{
	position:relative;
	width:auto;
	border:1px solid #DDD;
	background:#FFF;
	display:none;
	margin:0 0 8px;
	padding:16px;
}
.post-post-preview .post-post-preview-title, .post-post-preview .post-post-preview-title:focus{
	background:transparent;
	border:0;
	font-weight:bold;
	padding:0;
	font-size:14px;
	width:auto;
	margin:0 8px;
}
.post-post-preview .post-post-preview-description, .post-post-preview .post-post-preview-description:focus{
	background:transparent;
	border:0;
	font-weight:lighter;
	font-style:italic;
	padding:0;
	overflow:hidden;
	width:80%;
	margin:0 8px;
}
.post-post-preview .post-post-preview-icon-img{
	float:left;
	/*height:36px;*/
	width: 100%;
}

/**
 * Subscriptions pages
 */
.elgg-form-subscriptions-add{
	width:600px;
	margin:auto;
	border:1px solid #DDD;
	border-radius:3px;
	padding:16px;
}
.elgg-form-subscriptions-add input[type=text]{
	padding:16px;
	border:1px solid #DDD;
}
