<?php
/**
 * A very quick and easy cache factory
 * @author Mark Harding
 */
namespace minds\core\data\cache;

class factory{

	static private $default  = 'apcu';
	
	/**
	 * Build the cacher
	 * @param $cacher - the cacher to use
	 * @throws EXCEPTION
	 * @return cacher object
	 */
	static public function build($cacher = NULL){
		if(!$cacher)
			$cacher = self::$default;
		
		$cacher = "\\minds\\core\\data\\cache\\$cacher";
		if(class_exists($cacher)){
			return new $cacher();
		}
		
		throw new \Exception('Cacher not found ' . $cacher);
	}
	
}
