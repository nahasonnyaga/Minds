<?php

require('engine/start.php');

$GUID = new GUID();

ini_set('memory_limit', '4G');
set_time_limit ( 0 );
error_reporting(E_ERROR);

$tables = array('objects_entity', 'users_entity', 'entities', 'entity_subtypes', 'entity_relationships', 'metadata', 'metastrings', 'private_settings');

//@todo make this recieve variab;es
$mysql = mysqli_connect("192.168.200.16","minds","","minds") or die("Error " . mysqli_error($link));

$data = new StdClass();

foreach($tables as $table){
	echo "Gathering table: $table... this may take a few minutes \n";
	$query = $mysql->query('SELECT * FROM minds.elgg_'.$table);
	//var_dump($data->$tables);
	while($row = mysqli_fetch_object($query)) {
		//guid or id?
		if($row->guid){
			$data->{$table}[$row->guid] = $row;
		} else {
			$data->{$table}[$row->id] = $row;
		}
	} 
}

echo "Complete! \n\n";

echo "Merging split entity tables \n";
foreach($data->entities as $guid => $entity){
	$table = $entity->type . 's_entity';
	$secondary =  $data->{$table}[$guid];
	if(!$secondary){ unset($data->entities[$guid]); continue; }

	foreach($secondary as $k => $v){
		$entity->$k = utf8_encode($v);
	}

	//fix subtype issue
	$entity->subtype = $data->entity_subtypes[$entity->subtype]->subtype;

	//make widgets into type
        if($entity->subtype == 'widget'){
                $entity->type = 'widget';
                $entity->subtype = '';
        }
	
	$data->entities[$guid] = $entity;
}

echo "Beginning metadata merge... \n";

//merge metadata and metastrings
foreach($data->metadata as $id => $metadata){
	$metadata->name = utf8_encode($data->metastrings[$metadata->name_id]->string);
	$metadata->value = utf8_encode($data->metastrings[$metadata->value_id]->string);
	$data->metadata[$id] = $metadata;

	//append this metadata to the entity
	$data->entities[$metadata->entity_guid]->{$metadata->name} = $metadata->value;
} 

echo "Merging private settings... \n";

foreach($data->private_settings as $id => $ps){
	$data->entities[$ps->entity_guid]->{$ps->name} = $ps->value;	
}

echo "Data merges complete... \n";

echo "Now saving entities to Cassandra... this may take a while \n";

$errors = array();

try{
	foreach($data->entities as $row){ 
		$guid = $GUID->migrate($row->guid);
		$row->guid = $guid;
		$row->owner_guid = $GUID->migrate($row->owner_guid);//we need to move owners too
		$row->access_id = (int)$row->access_id;

		if($row->type =='group' || $row->subtype == 'oauth2_access_token' || $row->subtype == 'plugin' ||  $row->subtype == 'oauthnonce' || !$row->type){ continue; }
		$entity = entity_row_to_elggstar($row, $row->type);
		$entity->save();
		echo "Migrated: {$row->type}:{$row->subtype}:$guid \n";
	}

	echo "\n\n Beginning subscriptions transfer \n";

	foreach($data->entity_relationships as $relationship){
		if($relationship->relationship == 'friend'){
			$user = get_entity( $GUID->migrate($relationship->guid_one), 'user');
			$user2 = get_entity( $GUID->migrate($relationship->guid_two),'user');
			$user->addFriend( $GUID->migrate($relationship->guid_two));
		echo "{$user->name} is now following {$user2->name}\n";
		}
	}

echo "Migration complete... please test!\n";
} catch(Exception $e){
	$errors[] = $e->getMessage();
}

if(!empty($errors)){
	$count = count($errors);
	echo "There were $count errors:\n";
	foreach($errors as $error){
		echo $error;
	}
}

