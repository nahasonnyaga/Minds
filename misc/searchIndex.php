<?php

require_once(dirname(dirname(__FILE__)) . '/engine/start.php');

try{
	$client = new \Elasticsearch\Client(array('hosts'=>array('10.0.5.10')));
//	$client->indices()->create(array('index'=>'minds'));
}catch(Exception $e){
	var_dump($e); exit;
}
$offset = '';
while(true){
	try{
		foreach(elgg_get_entities(array('type'=>'user','limit'=>400, 'offset'=>$offset)) as $entity){
			if($entity->access_id == 2)
				$ret = \minds\plugin\search\start::createDocument($entity);
			
			echo "done $entity->guid \n";
			$offset = $entity->guid;
		}
	}catch(Exception $e){
		var_dump($e);
		exit;
	}
}
