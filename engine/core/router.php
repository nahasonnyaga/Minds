<?php
/**
 * The minds router. 
 */
namespace minds\core;

class router{
	
	static $routes = array();
	
	/**
	 * Route the pages
	 * (fallback to elgg page handler if we fail)
	 * 
	 */
	public function route($uri = null, $method = null){
		
		if(!$uri)	
			$route = strtok($_SERVER["REQUEST_URI"],'?');
		else
			$route = $uri;

		$route = rtrim($route, '/');
		$segments = explode('/', $route);
		$method = $method ? $method : strtolower($_SERVER['REQUEST_METHOD']);
	
		$loop = count($segments);

		while($loop >= 0){
			
			$offset = $loop -1;	
			
			if($loop < count($segments)){
				$slug_length = strlen($segments[$offset+1].'/');
				$route_length = strlen($route);
				$route = substr($route, 0, $route_length-$slug_length);
			}
		
			if(isset(self::$routes[$route])){
				$handler = new self::$routes[$route]();
				$pages = array_splice($segments, $loop) ?: array();
				return $handler->$method($pages);
			} 
			--$loop;
		}

		if($uri){
			$path = explode('/', substr($uri,1));
			
			$handler = array_shift($path);
			$page = implode('/',$path);
		} else {
			$handler = \get_input('handler');
			$page = \get_input('page');
		}


		return $this->legacyRoute($handler, $page);
	
	}
	
	/**
	 * Legacy fallback...
	 */
	public function legacyRoute($handler, $page){
		if (!\page_handler($handler, $page)) {
			//try a profile then
			if(!\page_handler('channel', "$handler/$page")){
				//forward('', '404');
				header("HTTP/1.0 404 Not Found");
				$buttons = \elgg_view('output/url', array('onclick'=>'window.history.back()', 'text'=>'Go back...', 'class'=>'elgg-button elgg-button-action'));
				$header = <<<HTML
<div class="elgg-head clearfix">
	<h2>404</h2>
	<h3>Ooooopppsss.... we couldn't find the page you where looking for! </h3>
	<div class="front-page-buttons">
		$buttons
	</div>
</div>
HTML;
				$body = \elgg_view_layout( "one_column", array(
							'content' => null, 
							'header'=>$header
						));
				echo \elgg_view_page('404', $body);
			}
		}
	}
	
	/**
	 * Register routes...
	 * 
	 * @param array $routes - an array of routes to handlers
	 * @return array - the array of all your routes
	 */
	static public function registerRoutes($routes = array()){
		return self::$routes = array_merge(self::$routes, $routes);
	}
}
