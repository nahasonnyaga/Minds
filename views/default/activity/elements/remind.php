<?php
$remind = $vars['remind'];
$entity = minds\core\entities::build(new minds\entities\entity($remind));

if(isset($remind['thumbnail_src']))
	$entity->thumbnail_src = $remind['thumbnail_src'];
else
	$entity->thumbnail_src = $entity->getIconUrl();
?>
<div class="activity-remind">
	<?= elgg_view_entity($entity,array('entity'=>$entity, 'comments'=>false, 'menu'=>false)) ?>
</div>