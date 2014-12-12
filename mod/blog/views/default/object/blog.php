<?php

/**
 * View for blog objects
 *
 * @package Blog
 */
$full = elgg_extract('full_view', $vars, FALSE);
$sidebar = elgg_extract('sidebar', $vars, FALSE);
$blog = elgg_extract('entity', $vars, FALSE);

if (!$blog) {
    return TRUE;
}

$owner = $blog->getOwnerEntity(true);
//$container = $blog->getContainerEntity();
$categories = elgg_view('output/categories', $vars);
$excerpt = elgg_get_excerpt($blog->excerpt, 200);
if (!$excerpt) {
    $excerpt = elgg_get_excerpt($blog->description);
}


$owner_icon = elgg_view_entity_icon($owner, 'small');

$owner_link = elgg_view('output/url', array(
    'href' => "blog/owner/$owner->username",
    'text' => $owner->name,
    'is_trusted' => true,
));

$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($blog->time_created);

$metadata = elgg_view_menu('entity', array(
    'entity' => $vars['entity'],
    'handler' => 'blog',
    'sort_by' => 'priority',
    'class' => $fulle ? 'elgg-menu-hz' : 'menu-v2',
    'full_view' => $full
));

$subtitle = "$author_text $date $comments_link $categories";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
    $metadata = '';
}

if ($full) {

    $body = elgg_view('output/longtext', array(
        'value' => $blog->description,
        'class' => 'blog-post clearfix',
    ));

    if(!$blog->rss_item_id){
        $ads = 2;
        $added =0;
        $length = strlen($body);
        $sep = "</p>";
        $paragraphs = explode($sep, $body);
        foreach($paragraphs as $i => $p){
       	   if($length < 300)
		break; //can't fit an ad here
            if(trim($p))
                $paragraphs[$i] .= $sep;
            if(($i % 10 == 0) && $added < $ads ){
                $paragraphs[$i] .= elgg_view('page/elements/ads', array(
					'type'=>'responsive-content', 
					'float'=> $added % 2 == 0 ? 'left' : 'right', 
					'height'=> $length > 2000 ? 'auto' : '280px', 
					'width'=> $length > 2000 ? '300' : '336px'
					));
                $added++;
            }
	    if($length < 2200)
    		break;
        }
        $body = implode('', $paragraphs);
	if($added == 1){
		$body .= elgg_view('page/elements/ads', array(
                                        'type'=>'responsive-content',
                                        'float'=> 'none',
                                        'height'=> 'auto',
                                        'width'=> '95%' 
                                        ));
	}

    }

    $body .= elgg_view('minds/license', array('license' => $blog->license));

    $body .= ' <i>This blog is free & open source, however embeds may not be. </i><br/>';

    //if blog is public, show social links
    if ($blog->access_id == 2) {
        $body .= elgg_view('minds_social/social_footer');
    }

    $params = array(
        'entity' => $blog,
        'title' => false,
        'metadata' => $metadata,
        'subtitle' => $subtitle,
    );
    $params = $params + $vars;
    $summary = elgg_view('object/elements/summary', $params);

    echo elgg_view('object/elements/full', array(
        //'summary' => $summary,
        //'icon' => $owner_icon,
        'body' => $body,
    ));
	
} elseif ($sidebar) {

    $image = elgg_view('output/img', array('src' => $owner_icon_url_large ? $owner_icon_url_large : minds_fetch_image($blog->description, $blog->owner_guid, 360), 'class' => 'rich-image'));
    $img_link = '<div class="rich-image-container">' . elgg_view('output/url', array('href' => $blog->getURL(), 'text' => $image)) . '</div>';
    $title = elgg_view('output/url', array('href' => $blog->getURL(), 'text' => '<h3>' . $blog->title . '</h3>', 'class' => 'title'));
    //echo elgg_view_image_block($img_link, $title, array('class'=>'rich-content sidebar'));
    echo $img_link;
    echo $title;
} else {
    // brief view
    $src = $blog->getIconURL();
    
    $class = 'rich-image';
    if (strpos($src, 'youtube') !== false) {
        $class .= ' youtube';
    }

    $image = elgg_view('output/img', array('src' => $src, 'class' => $class));
    $title = elgg_view('output/url', array('href' => $blog->getURL(), 'text' => elgg_view_title($blog->title)));
    $extras = '<p class="excerpt">' . elgg_view('output/url', array('href' => $blog->getURL(), 'text' => $excerpt)) . '</p>';
    if (!$owner) {
        return false;
    }
    $owner_link = elgg_view('output/url', array(
        'href' => $blog->ex_profile_url ? $blog->ex_profile_url : $owner->getURL(),
        'text' => $blog->ex_author ? $blog->ex_author : $owner->name
    ));

    $subtitle = '<i>' .
            elgg_echo('by') . ' ' . $owner_link . ' ' .
            elgg_view_friendly_time($blog->time_created) . '</i>';

    if($blog->viewcount){
		$count = number_format($blog->viewcount);
		$subtitle .= "<i> &bull; Views: $count+ </i>";
    }

    $header = elgg_view_image_block(elgg_view_entity_icon($owner, 'small'), $title . $subtitle);
  
    echo elgg_view('output/url', array('href' => $blog->getURL(), 'text' => $image, 'class' => 'blog-rich-image-holder'));
    echo $extras;
    echo $header;
    echo $metadata;
}
