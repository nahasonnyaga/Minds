<?php
/**
 * Minds theme
 *
 * @package Minds
 * @author Kramnorth (Mark Harding)
 *
 * 
 * Free & Open Source Social Media
 */
global $CONFIG;
$limit = get_input('limit', 12);
$offset = get_input('offset', 0);
$filter = get_input('filter', 'featured');

if($offset > 0 && $filter == 'featured'){
	$limit++;
}

if($filter == 'featured' && !get_input('timespan')){
	$entities = minds_get_featured('', $limit, 'entities',$offset); 
} else {
	//trending
	$options = array(
		'timespan' => get_input('timespan', 'day')
	);
	$trending = new MindsTrending(array(), $options);
	$guids = $trending->getList(array('limit'=> $limit, 'offset'=>$offset));
	if($guids){
		$entities = elgg_get_entities(array('guids'=>$guids, 'limit'=>$limit,'offset'=>0));
	} 
}

if(!elgg_is_logged_in()){
	$buttons = elgg_view('output/url', array('href'=>elgg_get_site_url().'register', 'text'=>elgg_echo('register'), 'class'=>'elgg-button elgg-button-action'));
} else {
	 $buttons = elgg_view('output/url', array('href'=>elgg_get_site_url().'archive/upload','text'=>elgg_echo('minds:archive:upload'), 'class'=>'elgg-button elgg-button-action'));
	 $buttons .= elgg_view('output/url', array('href'=>elgg_get_site_url().'blog/add','text'=>elgg_echo('blog:add'), 'class'=>'elgg-button elgg-button-action'));

}

//$buttons .= elgg_view('output/url', array('href'=>elgg_get_site_url().'nodes/launch', 'text'=>elgg_echo('register:node'), 'class'=>'elgg-button elgg-button-action'));

//$user_count = elgg_get_entities(array('type'=>'user', 'count'=>true));
//$max = 1000000;
//$countdown = $max - $user_count;
//if(strpos(elgg_get_site_url(), 'www.minds.com/') !== FALSE)
//	$subtitle = "$countdown more human sign-ups until automatic global <a href='release'><b>code release</b></a>.";
if(!get_input('ajax'))
$title = elgg_view('output/carousel', array('divs'=>$titles_array, 'subtitle'=> $subtitle));

$featured_item_class = $filter == 'featured' ? 'elgg-state-selected' : null;
$trending_item_class = $filter == 'trending' ? 'elgg-state-selected' : null;

$trending_menu = elgg_view_menu('trending');

if(elgg_is_sticky_form('register'))
extract(elgg_get_sticky_values('register'));
$signup_form = elgg_is_logged_in() ? '' : <<<HTML
<div class="frontpage-signup">
		<form action="action/register">
			<input type="text" name="u" placeholder="username" value="$u" autocomplete="off"/>
			<input type="text" name="e" placeholder="email" value="$e" autocomplete="off"/>
			<input type="password" name="p" value="$p" placeholder="password" autocomplete="off"/>
			<input type="hidden" name="tcs" value="true"/>
			<input type="submit" value="Sign up" class="elgg-button elgg-button-submit"/>
		</form>
	</div>
HTML;
$header = <<<HTML
<div class="elgg-head homepage clearfix">
	$title
	<ul class="elgg-menu elgg-menu-right-filter elgg-menu-hz">
		<li class="elgg-menu-item-featured $featured_item_class">
			<a href="?filter=featured">Featured</a>
		</li>
		<li class="elgg-menu-item-trending $trending_item_class elgg-menu-item-hover-over">
                        <a href="?filter=trending">Trending</a>
			$trending_menu
                </li>
	</ul>
	$signup_form
</div>
HTML;

if($entities){
	$content = elgg_view_entity_list($entities, array('full_view'=>false), $offset, $limit, false, false, true);
} else {
	$content = '';
}

$params = array(	'content'=> $content, 
					'header'=> $header,
					'filter' => false
					);

$body = elgg_view_layout('one_column', $params);

echo elgg_view_page('', $body, 'default', array('class'=>'index'));
