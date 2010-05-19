<?php
/**
 * Elgg Groups css
 * 
 * @package groups
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd <info@elgg.com>
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */

?>
/* group listings */
.group_count {
	float:right;
}
.group_listings {
	/* wraps group lists on 
	latest discussion, newest, popular */
}
.entity_subtext.groups {
	float:right;
	width:300px;
	text-align: right;
	margin-left: 10px;
}
.entity_listing.topic:hover {
	background-color: white;
}


/* group invitations */
.group_invitations a.action_button,
.group_invitations a.submit_button {
	float:right;
	margin:0 0 0 14px;
}


/* GROUPS SIDEBAR ELEMENTS */
#groupsearchform .search_input {
	width:196px;
}
.featured_group {
	margin-bottom:15px;
}
.featured_group .usericon {
	float:left;
	margin-right:10px;
}
.featured_group p.entity_title {
	margin-bottom:0;
}
.member_icon {
	margin:6px 6px 0 0;
	float:left;
}


/* GROUP PROFILE PAGE (individual group homepage) */
.group_profile_column {
	float:left;
	margin-top:10px;
}
.group_profile_column.icon {
	width:200px;
}
.group_profile_column.info {
	width:510px;
	margin-left:20px;
}
.group_profile_icon {
	width:200px;
	height:200px;
}
.group_stats {
	background: #eeeeee;
	padding:5px;
	margin-top:10px;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}
.group_stats p {
	margin:0;
}
.group_profile_column .odd,
.group_profile_column .even {
	background:#f4f4f4;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	padding:2px 4px;
	margin:0 0 7px;
}

/* tool content boxes on group profile page */
#group_tools_latest {
	min-height: 300px;
	margin-top:20px;
}
.group_tool_widget {
	float:left;
	margin-right:30px;
	margin-bottom:40px;
	min-height:200px;
	width:350px;
}
.group_tool_widget.odd {
	margin-right:0;
}
.group_tool_widget h3 {
	border-bottom:1px solid #CCCCCC;	
	background:#e4e4e4;
	color:#333333;
	padding:5px 5px 3px 5px;
	-moz-border-radius-topleft:4px;
	-moz-border-radius-topright:4px;
	-webkit-border-top-left-radius:4px;
	-webkit-border-top-right-radius:4px;
}

/* group activity latest
	(hide some items used on the full riverdashboard activity) 
	@todo provide a separate view for a groups latest activity
	- so we can have tiny avatars and not have to manually hide elements
*/
.group_tool_widget.activity a.river_comment_form_button,
.group_tool_widget.activity .river_comments_tabs,
.group_tool_widget.activity .river_content_display,
.group_tool_widget.activity .river_comments,
.group_tool_widget.activity .river_link_divider,
.group_tool_widget.activity .river_user_like_button {
	display:none;
}
.group_tool_widget.activity .river_item .entity_subtext {
	padding:0;
}

/* override default entity_listing_info width */
.group_tool_widget .entity_listing_info {
	width:315px;
}

/* edit group page */
.delete_group {
	float: right;
	margin-top:-44px;
}

/* edit forum posts
   - force tinyMCE to correct width */
.edit_comment .defaultSkin table.mceLayout {
	width: 694px !important;
}

