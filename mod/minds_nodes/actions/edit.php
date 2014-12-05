<?php

$node_guid = get_input('node_guid');
$node = new MindsNode($node_guid);

if(!$node->domain){
	register_error('The following domain could not be found');
	return false;
}

$domain_at_minds = get_input('domain_at_minds');

$domain = get_input("domain");

if($node->allowedDomain() && $domain){
	$domain = $domain;
} else {
	$domain = $domain_at_minds . '.minds.com';
}

try{
	//$node->domain = $domain;
	$node->renameNode($domain);
	$node->save();
} catch (\Exception $e){
	register_error($e->getMessage());
}
