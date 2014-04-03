<?php 
	$user = elgg_extract('user', $vars, elgg_get_logged_in_user_entity());
?>
<div class="channel-social-icons">
			<?php
				$network_icons = array('fb'=>'&#62221;', 'twitter'=>'&#62218;', 'gplus'=>'&#62224;', 'tumblr'=>'&#62230;', 'linkedin'=>'&#62233;');
				foreach($user as $k=>$v){
					if(strpos($k, 'social_link_') !==FALSE && $v){
						$network = str_replace('social_link_', '', $k);
						echo elgg_view('output/url', array('text'=>$network_icons[$network], 'href'=>$v, 'class'=>'entypo'));
					}
				}
			?>
		</div>
