<?php
/**
 * Minds Search CC Article View
 */

$article = $vars['article']; 
$full_view = $vars['full_view'];

$title = strlen($article['title'])>25 ? substr($article['title'], 0, 25) . '...' : $article['title'];
$url = elgg_get_site_url().'search/result/'.$article['id'];
$source = $article['source'];
$description = $article['description'];

if(!$full_view){
	
?>
<a href='<?php echo $url;?>'>
	<div class='minds-search minds-search-item'>
		<h3><?php echo $title;?></h3>
		<p><?php echo $description;?> <br/>
		<b><?php echo $source;?></b> <br/>
	</p>
	</div>
</a>
<?php 
}else {
	minds_set_metatags('og:title', $article['title']);
	minds_set_metatags('og:type', 'mindscom:photo');
	minds_set_metatags('og:url', $url);
	minds_set_metatags('og:image', $imageURL);
	minds_set_metatags('mindscom:photo', $imageURL);
	minds_set_metatags('og:description', 'License: ' . elgg_echo('minds:license:'.$article['license']));
	
	if($source=='wikipedia'){
		elgg_load_css('wiki');
		$url = 'http://en.wikipedia.org/w/api.php?action=parse&page=' . urlencode($article['title']) .'&format=json&rvprop=content';
		$ch = curl_init($url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_USERAGENT, "mindsDOTCOM"); // required by wikipedia.org server; use YOUR user agent with YOUR contact information. (otherwise your IP might get blocked)
		$c = curl_exec($ch);
			
		$json = json_decode($c);
			 
		$content = $json->{'parse'}->{'text'}->{'*'};
		
		//@todo Make sure all links go to wikipedia
		
		echo "<div style='clear:both;margin-top:10px;'>";
		echo $content;
		echo "</div>";
	}elseif($source=='minds'){
		forward($article['href']);
	}
}?>