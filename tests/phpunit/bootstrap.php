<?php
global $CONFIG;
$root = dirname(dirname(dirname(__FILE__)));

define('__MINDS_ROOT__', $root);

require_once($root.'/vendor/autoload.php');

$minds = new Minds\Core\Minds();
$minds->loadConfigs();
$minds->loadLegacy();

@mkdir('/tmp/minds_test/', 777);
$CONFIG->dataroot = '/tmp/minds_test/';

//require_once("$engine/settings.php");


require_once(dirname(__FILE__) .'/Minds_PHPUnit_Framework_TestCase.php');

/*spl_autoload_register(function($class){

});*/

date_default_timezone_set('America/Los_Angeles');

error_reporting(E_ALL | E_STRICT);

/**
 * Check to see if we are installed. If not, install
 */
try{
  $CONFIG->cassandra = (object) array(
    'servers'=> array('localhost'),
    'keyspace'=>'unit_tests',
    'cql_servers'=> array('localhost'));

	//bootstrap the cassandra config

	$CONFIG->default_access = 2; //public access

	$site = new ElggSite();
	$site->name = 'Minds';
	$site->email = "dev@minds.io";
	$site_guid = $site->save();

	// bootstrap site info
	$CONFIG->site_guid = $site_guid;
	$CONFIG->site = $site;
	$CONFIG->site_email = $site->email;

  $CONFIG->mongodb_db = 'unittest';
  $CONFIG->rabbitmq = array(
    'host' => 'unit_tests'
  );

  //for testing email encryption/decryption
  $CONFIG->encryptionKeys = array(
      'email' => array(
          'public' => dirname(__FILE__) . '/keys/email-public.key',
          'private' => dirname(__FILE__) . '/keys/email-private.key'
      ));
}catch(Exception $e){
	var_dump($e);
	exit;
}
