<?php
/**
 * Minds Search CC User View
 * 
 */
 
$user= get_entity($vars['user']['guid']);
?>
<a href='<?php echo $user->getURL();?>'>
	<div class='minds-search minds-search-item'>
		<?php echo elgg_view('output/img', array('src'=>$user->getIconURL('large')));?>
		<h3><?php echo $user->name;?></h3>
		<p><b>minds channel</b></p>
	</div>
</a>