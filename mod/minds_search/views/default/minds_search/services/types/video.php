<?php
/**
 * Minds Search CC Image View
 */
 
$video = $vars['video'];
$full_view = $vars['full_view'];

$title = strlen($video['title'])>60 ? substr($video['title'], 0, 60) . '...' : $video['title'];
$img = elgg_view('output/img', array('src'=>$video['iconURL']));
$source = $video['source'];

if($source == 'minds'){
	$entity = get_entity($video['guid']);
	$iconURL = kaltura_get_thumnail($entity->kaltura_video_id, 160, 100, 100, 5);
	$img = "<img src='".$iconURL."'/>";
	$source = "minds media";
} else {
	$source = "Source: " . $video['source'] . "<br/> Type: Video";
}

if(!$full_view){
?>
<a href='<?php echo elgg_get_site_url().'search/result/'.$video['id'];?>'>
	<div class='minds-search minds-search-item minds-search-item-video'>
		<span></span>
		<?php echo $img;?>
		<h3><?php echo $title;?></h3>
		<p><b><?php echo $source; ?></b></p>
	</div>
</a>
<?php 
}else {
	minds_set_metatags('og:title', $video['title']);
	minds_set_metatags('og:type', 'video');
	minds_set_metatags('og:url', $url);
	minds_set_metatags('og:image', $video['iconURL']);
	minds_set_metatags('og:description', 'License: ' . elgg_echo('minds:license:'.$video['license']));
	if($source=='archive.org'){
		forward($video['href']);
	}elseif($source=='youtube'){
		$yt_id = str_replace('youtube_', '', $video['id']);
		echo '<iframe src="http://youtube.com/embed/'.$yt_id.'" width="975px" height="500px"></iframe>';
		minds_set_metatags('og:video', 'http://youtube.com/v/'.$yt_id);
		minds_set_metatags('og:video:secure_url', 'https://youtube.com/v/'.$yt_id);
		minds_set_metatags('og:video:type', 'application/x-shockwave-flash');
		minds_set_metatags('og:video:width', 1280);
		minds_set_metatags('og:video:height', 720);
	} elseif($source=='minds'){
		forward($entity->getURL());
	}
}
