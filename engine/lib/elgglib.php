<?php
/**
 * Elgg library
 * Contains important functionality core to Elgg
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

/**
 * Getting directories and moving the browser
 */

/**
 * Forwards the browser.
 * Returns false if headers have already been sent and the browser cannot be moved.
 *
 * @param string $location URL to forward to browser to. Can be relative path.
 * @return nothing|false
 */
function forward($location = "") {
	global $CONFIG;

	if (!headers_sent()) {
		if ($location === REFERER) {
			$location = $_SERVER['HTTP_REFERER'];
		}

		$current_page = current_page_url();
		if ((substr_count($location, 'http://') == 0) && (substr_count($location, 'https://') == 0)) {
			$location = $CONFIG->url . $location;
		}

		// return new forward location or false to stop the forward or empty string to exit
		$params = array('current_url' => $current_page, 'forward_url' => $location);
		$location = trigger_plugin_hook('forward', 'system', $params, $location);
		if ($location) {
			header("Location: {$location}");
			exit;
		} else if ($location === '') {
			exit;
		}
	}

	return false;
}

/**
 * Return the current page URL.
 */
function current_page_url() {
	global $CONFIG;

	$url = parse_url($CONFIG->wwwroot);

	$page = $url['scheme'] . "://";

	// user/pass
	if ((isset($url['user'])) && ($url['user'])) {
		$page .= $url['user'];
	}
	if ((isset($url['pass'])) && ($url['pass'])) {
		$page .= ":".$url['pass'];
	}
	if ((isset($url['user']) && $url['user']) ||
		(isset($url['pass']) && $url['pass'])) {
		$page .="@";
	}

	$page .= $url['host'];

	if ((isset($url['port'])) && ($url['port'])) {
		$page .= ":" . $url['port'];
	}

	//$page.="/";
	$page = trim($page, "/");

	$page .= $_SERVER['REQUEST_URI'];

	return $page;
}

/**
 * Templating and visual functionality
 */

$CURRENT_SYSTEM_VIEWTYPE = "";

/**
 * Override the view mode detection for the elgg view system.
 *
 * This function will force any further views to be rendered using $viewtype. Remember to call elgg_set_viewtype() with
 * no parameters to reset.
 *
 * @param string $viewtype The view type, e.g. 'rss', or 'default'.
 * @return bool
 */
function elgg_set_viewtype($viewtype = "") {
	global $CURRENT_SYSTEM_VIEWTYPE;

	$CURRENT_SYSTEM_VIEWTYPE = $viewtype;

	return true;
}

/**
 * Return the current view type used by the elgg view system.
 *
 * By default, this function will return a value based on the default for your system or from the command line
 * view parameter. However, you may force a given view type by calling elgg_set_viewtype()
 *
 * @return string The view.
 */
function elgg_get_viewtype() {
	global $CURRENT_SYSTEM_VIEWTYPE, $CONFIG;

	$viewtype = NULL;

	if ($CURRENT_SYSTEM_VIEWTYPE != "") {
		return $CURRENT_SYSTEM_VIEWTYPE;
	}

	if ((empty($_SESSION['view'])) || ( (trim($CONFIG->view!="")) && ($_SESSION['view']!=$CONFIG->view) )) {
		$_SESSION['view'] = "default";
		// If we have a config default view for this site then use that instead of 'default'
		if (/*(is_installed()) && */(!empty($CONFIG->view)) && (trim($CONFIG->view)!="")) {
			$_SESSION['view'] = $CONFIG->view;
		}
	}

	if (empty($viewtype) && is_callable('get_input')) {
		$viewtype = get_input('view');
	}

	if (empty($viewtype)) {
		$viewtype = $_SESSION['view'];
	}

	return $viewtype;
}

/**
 * Register a viewtype to fall back to a default view if view does not exist in
 * that viewtype.
 *
 * This is useful for alternate html viewtypes (such as for mobile devices)
 *
 * @param string $viewtype The viewtype to register
 */
function elgg_register_viewtype_fallback($viewtype) {
	global $CONFIG;

	if (!isset($CONFIG->viewtype)) {
		$CONFIG->viewtype = new stdClass;
	}

	if (!isset($CONFIG->viewtype->fallback)) {
		$CONFIG->viewtype->fallback = array();
	}

	$CONFIG->viewtype->fallback[] = $viewtype;
}

/**
 * Checks if this viewtype falls back to default
 *
 * @param string $viewtype
 * @return boolean
 */
function elgg_does_viewtype_fallback($viewtype) {
	global $CONFIG;

	if (isset($CONFIG->viewtype) && isset($CONFIG->viewtype->fallback)) {
		return in_array($viewtype, $CONFIG->viewtype->fallback);
	}

	return FALSE;
}


/**
 * Return the location of a given view.
 *
 * @param string $view The view.
 * @param string $viewtype The viewtype
 */
function elgg_get_view_location($view, $viewtype = '') {
	global $CONFIG;

	if (empty($viewtype)) {
		$viewtype = elgg_get_viewtype();
	}

	if (!isset($CONFIG->views->locations[$viewtype][$view])) {
		if (!isset($CONFIG->viewpath)) {
			return dirname(dirname(dirname(__FILE__))) . "/views/";
		} else {
			return $CONFIG->viewpath;
		}
	} else {
		return $CONFIG->views->locations[$viewtype][$view];
	}

	return false;
}

/**
 * Handles templating views
 *
 * @see set_template_handler
 *
 * @param string $view The name and location of the view to use
 * @param array $vars Any variables that the view requires, passed as an array
 * @param boolean $bypass If set to true, elgg_view will bypass any specified alternative template handler; by default, it will hand off to this if requested (see set_template_handler)
 * @param boolean $debug If set to true, the viewer will complain if it can't find a view
 * @param string $viewtype If set, forces the viewtype for the elgg_view call to be this value (default: standard detection)
 * @return string The HTML content
 */
function elgg_view($view, $vars = array(), $bypass = false, $debug = false, $viewtype = '') {
	global $CONFIG;
	static $usercache;

	$view = (string)$view;

	// basic checking for bad paths
	if (strpos($view, '..') !== false) {
		return false;
	}

	$view_orig = $view;

	// Trigger the pagesetup event
	if (!isset($CONFIG->pagesetupdone)) {
		trigger_elgg_event('pagesetup','system');
		$CONFIG->pagesetupdone = true;
	}

	if (!is_array($usercache)) {
		$usercache = array();
	}

	if (!is_array($vars)) {
		elgg_log('Vars in views must be an array!', 'ERROR');
		$vars = array();
	}

	if (empty($vars)) {
		$vars = array();
	}

	// Load session and configuration variables into $vars
	// $_SESSION will always be an array if it is set
	if (isset($_SESSION) /*&& is_array($_SESSION)*/ ) {
		//= array_merge($vars, $_SESSION);
		$vars += $_SESSION;
	}

	$vars['config'] = array();

	if (!empty($CONFIG)) {
		$vars['config'] = $CONFIG;
	}

	$vars['url'] = $CONFIG->url;

	// Load page owner variables into $vars
	if (is_callable('page_owner')) {
		$vars['page_owner'] = page_owner();
	} else {
		$vars['page_owner'] = -1;
	}

	if (($vars['page_owner'] != -1) && (is_installed())) {
		if (!isset($usercache[$vars['page_owner']])) {
			$vars['page_owner_user'] = get_entity($vars['page_owner']);
			$usercache[$vars['page_owner']] = $vars['page_owner_user'];
		} else {
			$vars['page_owner_user'] = $usercache[$vars['page_owner']];
		}
	}

	if (!isset($vars['js'])) {
		$vars['js'] = "";
	}

	// If it's been requested, pass off to a template handler instead
	if ($bypass == false && isset($CONFIG->template_handler) && !empty($CONFIG->template_handler)) {
		$template_handler = $CONFIG->template_handler;
		if (is_callable($template_handler)) {
			return $template_handler($view, $vars);
		}
	}

	// Get the current viewtype
	if (empty($viewtype)) {
		$viewtype = elgg_get_viewtype();
	}

	// Viewtypes can only be alphanumeric
	if (preg_match('[\W]', $viewtype)) {
		return '';
	}

	// Set up any extensions to the requested view
	if (isset($CONFIG->views->extensions[$view])) {
		$viewlist = $CONFIG->views->extensions[$view];
	} else {
		$viewlist = array(500 => $view);
	}

	// Start the output buffer, find the requested view file, and execute it
	ob_start();

	foreach($viewlist as $priority => $view) {
		$view_location = elgg_get_view_location($view, $viewtype);
		$view_file = "$view_location$viewtype/$view.php";
		$default_view_file = "{$view_location}default/$view.php";

		// try to include view
		if (!file_exists($view_file) || !include($view_file)) {
			// requested view does not exist
			$error = "$viewtype/$view view does not exist.";

			// attempt to load default view
			if ($viewtype != 'default' && elgg_does_viewtype_fallback($viewtype)) {
				if (file_exists($default_view_file) && include($default_view_file)) {
					// default view found
					$error .= " Using default/$view instead.";
				} else {
					// no view found at all
					$error = "Neither $viewtype/$view nor default/$view view exists.";
				}
			}

			// log warning
			elgg_log($error, 'NOTICE');
		}
	}

	// Save the output buffer into the $content variable
	$content = ob_get_clean();

	// Plugin hook
	$content = trigger_plugin_hook('view', $view_orig,
		array('view' => $view_orig, 'vars' => $vars), $content);

	// backward compatibility with less grandular hook will be gone in 2.0
	$content_tmp = trigger_plugin_hook('display', 'view', array('view' => $view_orig, 'vars' => $vars), $content);

	if ($content_tmp != $content) {
		$content = $content_tmp;
		elgg_deprecated_notice('The display:view plugin hook is deprecated by view:view_name or view:all', 1.8);
	}

	// Return $content
	return $content;
}

/**
 * Returns whether the specified view exists
 *
 * @param string $view The view name
 * @param string $viewtype If set, forces the viewtype
 * @param bool $recurse If false, do not recursively check extensions
 * @return true|false Depending on success
 */
function elgg_view_exists($view, $viewtype = '', $recurse = true) {
	global $CONFIG;

	// Detect view type
	if (empty($viewtype)) {
		$viewtype = elgg_get_viewtype();
	}

	if (!isset($CONFIG->views->locations[$viewtype][$view])) {
		if (!isset($CONFIG->viewpath)) {
			$location = dirname(dirname(dirname(__FILE__))) . "/views/";
		} else {
			$location = $CONFIG->viewpath;
		}
	} else {
		$location = $CONFIG->views->locations[$viewtype][$view];
	}

	if (file_exists($location . "{$viewtype}/{$view}.php")) {
		return true;
	}

	// If we got here then check whether this exists as an extension
	// We optionally recursively check whether the extended view exists also for the viewtype
	if ($recurse && isset($CONFIG->views->extensions[$view])) {
		foreach( $CONFIG->views->extensions[$view] as $view_extension ) {
			// do not recursively check to stay away from infinite loops
			if (elgg_view_exists($view_extension, $viewtype, false)) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Registers a view to be simply cached
 *
 * Views cached in this manner must take no parameters and be login agnostic -
 * that is to say, they look the same no matter who is logged in (or logged out).
 *
 * CSS and the basic jS views are automatically cached like this.
 *
 * @param string $viewname View name
 */
function elgg_view_register_simplecache($viewname) {
	global $CONFIG;

	if (!isset($CONFIG->views)) {
		$CONFIG->views = new stdClass;
	}

	if (!isset($CONFIG->views->simplecache)) {
		$CONFIG->views->simplecache = array();
	}

	//if (elgg_view_exists($viewname))
	$CONFIG->views->simplecache[] = $viewname;
}

/**
 * Regenerates the simple cache.
 *
 * @see elgg_view_register_simplecache
 *
 */
function elgg_view_regenerate_simplecache() {
	global $CONFIG;

	// @todo elgg_view() checks if the page set is done (isset($CONFIG->pagesetupdone)) and
	// triggers an event if it's not. Calling elgg_view() here breaks submenus
	// (at least) because the page setup hook is called before any
	// contexts can be correctly set (since this is called before page_handler()).
	// To avoid this, lie about $CONFIG->pagehandlerdone to force
	// the trigger correctly when the first view is actually being output.
	$CONFIG->pagesetupdone = TRUE;

	if (isset($CONFIG->views->simplecache)) {
		if (!file_exists($CONFIG->dataroot . 'views_simplecache')) {
			@mkdir($CONFIG->dataroot . 'views_simplecache');
		}

		if (!empty($CONFIG->views->simplecache) && is_array($CONFIG->views->simplecache)) {
			foreach($CONFIG->views->simplecache as $view) {
				$viewcontents = elgg_view($view);
				$viewname = md5(elgg_get_viewtype() . $view);
				if ($handle = fopen($CONFIG->dataroot . 'views_simplecache/' . $viewname, 'w')) {
					fwrite($handle, $viewcontents);
					fclose($handle);
				}
			}
		}

		datalist_set('simplecache_lastupdate', 0);
	}

	unset($CONFIG->pagesetupdone);
}

/**
 * Enables the simple cache.
 *
 * @see elgg_view_register_simplecache
 *
 */

function elgg_view_enable_simplecache() {
	global $CONFIG;

	datalist_set('simplecache_enabled',1);
	$CONFIG->simplecache_enabled = 1;
	elgg_view_regenerate_simplecache();
}

/**
 * Disables the simple cache.
 *
 * @see elgg_view_register_simplecache
 *
 */
function elgg_view_disable_simplecache() {
	global $CONFIG;
	if ($CONFIG->simplecache_enabled) {
		datalist_set('simplecache_enabled',0);
		$CONFIG->simplecache_enabled = 0;

		// purge simple cache
		if ($handle = opendir($CONFIG->dataroot.'views_simplecache')) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					unlink($CONFIG->dataroot.'views_simplecache/'.$file);
				}
			}
			closedir($handle);
		}
	}
}

/**
 * This is a factory function which produces an ElggCache object suitable for caching file load paths.
 *
 * TODO: Can this be done in a cleaner way?
 * TODO: Swap to memcache etc?
 */
function elgg_get_filepath_cache() {
	global $CONFIG;
	static $FILE_PATH_CACHE;
	if (!$FILE_PATH_CACHE) $FILE_PATH_CACHE = new ElggFileCache($CONFIG->dataroot);

	return $FILE_PATH_CACHE;
}

/**
 * Function which resets the file path cache.
 *
 */
function elgg_filepath_cache_reset() {
	$cache = elgg_get_filepath_cache();
	return $cache->delete('view_paths');
}

/**
 * Saves a filepath cache.
 *
 * @param mixed $data
 */
function elgg_filepath_cache_save($data) {
	global $CONFIG;

	if ($CONFIG->viewpath_cache_enabled) {
		$cache = elgg_get_filepath_cache();
		return $cache->save('view_paths', $data);
	}

	return false;
}

/**
 * Retrieve the contents of the filepath cache.
 *
 */
function elgg_filepath_cache_load() {
	global $CONFIG;

	if ($CONFIG->viewpath_cache_enabled) {
		$cache = elgg_get_filepath_cache();
		$cached_view_paths = $cache->load('view_paths');

		if ($cached_view_paths) {
			return $cached_view_paths;
		}
	}

	return NULL;
}

/**
 * Enable the filepath cache.
 *
 */
function elgg_enable_filepath_cache() {
	global $CONFIG;

	datalist_set('viewpath_cache_enabled',1);
	$CONFIG->viewpath_cache_enabled = 1;
	elgg_filepath_cache_reset();
}

/**
 * Disable filepath cache.
 *
 */
function elgg_disable_filepath_cache() {
	global $CONFIG;

	datalist_set('viewpath_cache_enabled',0);
	$CONFIG->viewpath_cache_enabled = 0;
	elgg_filepath_cache_reset();
}

/**
 * Internal function for retrieving views used by elgg_view_tree
 *
 * @param unknown_type $dir
 * @param unknown_type $base
 * @return unknown
 */
function elgg_get_views($dir, $base) {
	$return = array();
	if (file_exists($dir) && is_dir($dir)) {
		if ($handle = opendir($dir)) {
			while ($view = readdir($handle)) {
				if (!in_array($view, array('.','..','.svn','CVS'))) {
					if (is_dir($dir . '/' . $view)) {
						if ($val = elgg_get_views($dir . '/' . $view, $base . '/' . $view)) {
							$return = array_merge($return, $val);
						}
					} else {
						$view = str_replace('.php','',$view);
						$return[] = $base . '/' . $view;
					}
				}
			}
		}
	}
	return $return;
}

/**
 * @deprecated 1.7.  Use elgg_get_views().
 * @param $dir
 * @param $base
 */
function get_views($dir, $base) {
	elgg_deprecated_notice('get_views() was deprecated by elgg_get_views()!', 1.7);
	elgg_get_views($dir, $base);
}

/**
 * When given a partial view root (eg 'js' or 'page_elements'), returns an array of views underneath it
 *
 * @param string $view_root The root view
 * @param string $viewtype Optionally specify a view type other than the current one.
 * @return array A list of view names underneath that root view
 */
function elgg_view_tree($view_root, $viewtype = "") {
	global $CONFIG;
	static $treecache;

	// Get viewtype
	if (!$viewtype) {
		$viewtype = elgg_get_viewtype();
	}

	// Has the treecache been initialised?
	if (!isset($treecache)) {
		$treecache = array();
	}
	// A little light internal caching
	if (!empty($treecache[$view_root])) {
		return $treecache[$view_root];
	}

	// Examine $CONFIG->views->locations
	if (isset($CONFIG->views->locations[$viewtype])) {
		foreach($CONFIG->views->locations[$viewtype] as $view => $path) {
			$pos = strpos($view,$view_root);
			if ($pos === 0) {
				$treecache[$view_root][] = $view;
			}
		}
	}

	// Now examine core
	$location = $CONFIG->viewpath;
	$viewtype = elgg_get_viewtype();
	$root = $location . $viewtype . '/' . $view_root;

	if (file_exists($root) && is_dir($root)) {
		$val = elgg_get_views($root, $view_root);
		if (!is_array($treecache[$view_root])) {
			$treecache[$view_root] = array();
		}
		$treecache[$view_root] = array_merge($treecache[$view_root], $val);
	}

	return $treecache[$view_root];
}

/**
 * When given an entity, views it intelligently.
 *
 * Expects a view to exist called entity-type/subtype, or for the entity to have a parameter
 * 'view' which lists a different view to display.  In both cases, elgg_view will be called with
 * array('entity' => $entity, 'full' => $full) as its parameters, and therefore this is what
 * the view should expect to receive.
 *
 * @param ElggEntity $entity The entity to display
 * @param boolean $full Determines whether or not to display the full version of an object, or a smaller version for use in aggregators etc
 * @param boolean $bypass If set to true, elgg_view will bypass any specified alternative template handler; by default, it will hand off to this if requested (see set_template_handler)
 * @param boolean $debug If set to true, the viewer will complain if it can't find a view
 * @return string HTML to display or false
 */
function elgg_view_entity(ElggEntity $entity, $full = false, $bypass = true, $debug = false) {
	global $autofeed;
	$autofeed = true;

	// No point continuing if entity is null
	if (!$entity) {
		return '';
	}

	if (!($entity instanceof ElggEntity)) {
		return false;
	}

	// if this entity has a view defined, use it
	$view = $entity->view;
	if (is_string($view)) {
		return elgg_view($view,
						array('entity' => $entity, 'full' => $full),
						$bypass,
						$debug);
	}

	$entity_type = $entity->getType();

	$subtype = $entity->getSubtype();
	if (empty($subtype)) {
		$subtype = $entity_type;
	}

	$contents = '';
	if (elgg_view_exists("{$entity_type}/{$subtype}")) {
		$contents = elgg_view("{$entity_type}/{$subtype}", array(
				'entity' => $entity,
				'full' => $full
				), $bypass, $debug);
	}
	if (empty($contents)) {
		$contents = elgg_view("{$entity_type}/default",array(
				'entity' => $entity,
				'full' => $full
				), $bypass, $debug);
	}
	// Marcus Povey 20090616 : Speculative and low impact approach for fixing #964
	if ($full)  {
		$annotations = elgg_view_entity_annotations($entity, $full);

		if ($annotations) {
			$contents .= $annotations;
		}
	}
	return $contents;
}

/**
 * When given an annotation, views it intelligently.
 *
 * This function expects annotation views to be of the form annotation/name, where name
 * is the type of annotation.
 *
 * @param ElggAnnotation $annotation The annotation to display
 * @param boolean $full Determines whether or not to display the full version of an object, or a smaller version for use in aggregators etc
 * @param boolean $bypass If set to true, elgg_view will bypass any specified alternative template handler; by default, it will hand off to this if requested (see set_template_handler)
 * @param boolean $debug If set to true, the viewer will complain if it can't find a view
 * @return string HTML (etc) to display
 */
function elgg_view_annotation(ElggAnnotation $annotation, $bypass = true, $debug = false) {
	global $autofeed;
	$autofeed = true;

	$view = $annotation->view;
	if (is_string($view)) {
		return elgg_view($view,array('annotation' => $annotation), $bypass, $debug);
	}

	$name = $annotation->name;
	$intname = (int) $name;
	if ("{$intname}" == "{$name}") {
		$name = get_metastring($intname);
	}
	if (empty($name)) {
		return "";
	}

	if (elgg_view_exists("annotation/{$name}")) {
		return elgg_view("annotation/{$name}",array('annotation' => $annotation), $bypass, $debug);
	} else {
		return elgg_view("annotation/default",array('annotation' => $annotation), $bypass, $debug);
	}
}


/**
 * Returns a view of a list of entities, plus navigation. It is intended that this function
 * be called from other wrapper functions.
 *
 * @see list_entities
 * @see list_user_objects
 * @see list_user_friends_objects
 * @see list_entities_from_metadata
 * @see list_entities_from_metadata_multi
 * @see list_entities_from_relationships
 * @see list_site_members
 *
 * @param array $entities List of entities
 * @param int $count The total number of entities across all pages
 * @param int $offset The current indexing offset
 * @param int $limit The number of entities to display per page
 * @param true|false $fullview Whether or not to display the full view (default: true)
 * @param true|false $viewtypetoggle Whether or not to allow users to toggle to gallery view
 * @param bool $pagination Whether pagination is offered.
 * @return string The list of entities
 */
function elgg_view_entity_list($entities, $count, $offset, $limit, $fullview = true, $viewtypetoggle = true, $pagination = true) {
	$count = (int) $count;
	$limit = (int) $limit;

	// do not require views to explicitly pass in the offset
	if (!$offset = (int) $offset) {
		$offset = sanitise_int(get_input('offset', 0));
	}

	// do not require views to explicitly pass in the offset
	if (!$offset = (int) $offset) {
		$offset = sanitise_int(get_input('offset', 0));
	}

	$context = get_context();

	$html = elgg_view('entities/entity_list',array(
		'entities' => $entities,
		'count' => $count,
		'offset' => $offset,
		'limit' => $limit,
		'baseurl' => $_SERVER['REQUEST_URI'],
		'fullview' => $fullview,
		'context' => $context,
		'viewtypetoggle' => $viewtypetoggle,
		'viewtype' => get_input('search_viewtype','list'),
		'pagination' => $pagination
	));

	return $html;
}

/**
 * Returns a view of a list of annotations, plus navigation. It is intended that this function
 * be called from other wrapper functions.
 *
 * @param array $annotations List of annotations
 * @param int $count The total number of annotations across all pages
 * @param int $offset The current indexing offset
 * @param int $limit The number of annotations to display per page
 * @return string The list of annotations
 */
function elgg_view_annotation_list($annotations, $count, $offset, $limit) {
	$count = (int) $count;
	$offset = (int) $offset;
	$limit = (int) $limit;

	$html = "";

	$nav = elgg_view('navigation/pagination',array(
		'baseurl' => $_SERVER['REQUEST_URI'],
		'offset' => $offset,
		'count' => $count,
		'limit' => $limit,
		'word' => 'annoff',
		'nonefound' => false,
	));

	$html .= $nav;

	if (is_array($annotations) && sizeof($annotations) > 0) {
		foreach($annotations as $annotation) {
			$html .= elgg_view_annotation($annotation, "", false);
		}
	}

	if ($count) {
		$html .= $nav;
	}

	return $html;
}

/**
 * Display a selective rendered list of annotations for a given entity.
 *
 * The list is produced as the result of the entity:annotate plugin hook
 * and is designed to provide a more generic framework to allow plugins
 * to extend the generic display of entities with their own annotation
 * renderings.
 *
 * This is called automatically by the framework from elgg_view_entity()
 *
 * @param ElggEntity $entity
 * @param bool $full
 * @return string or false on failure
 */
function elgg_view_entity_annotations(ElggEntity $entity, $full = true) {

	// No point continuing if entity is null
	if (!$entity) {
		return false;
	}

	if (!($entity instanceof ElggEntity)) {
		return false;
	}

	$entity_type = $entity->getType();

	$annotations = trigger_plugin_hook('entity:annotate', $entity_type,
		array(
			'entity' => $entity,
			'full' => $full,
		)
	);

	return $annotations;
}

/**
 * Displays an internal layout for the use of a plugin canvas.
 * Takes a variable number of parameters, which are made available
 * in the views as $vars['area1'] .. $vars['areaN'].
 *
 * @param string $layout The name of the views in canvas/layouts/.
 * @return string The layout
 */
function elgg_view_layout($layout) {
	$arg = 1;
	$param_array = array();
	while ($arg < func_num_args()) {
		$param_array['area' . $arg] = func_get_arg($arg);
		$arg++;
	}
	if (elgg_view_exists("canvas/layouts/{$layout}")) {
		return elgg_view("canvas/layouts/{$layout}",$param_array);
	} else {
		return elgg_view("canvas/default",$param_array);
	}
}

/**
 * Returns a view for the page title
 *
 * @param string $title The page title
 * @param string $submenu Should a submenu be displayed? (default false, use not recommended)
 * @return string The HTML (etc)
 */
function elgg_view_title($title, $submenu = false) {
	$title = elgg_view('page_elements/title', array('title' => $title, 'submenu' => $submenu));

	return $title;
}

/**
 * Deprecated by elgg_add_submenu_item()
 *
 * @see elgg_add_submenu_item()
 * @deprecated 1.8
 */
function add_submenu_item($label, $link, $group = 'default', $onclick = false, $selected = NULL) {
	elgg_deprecated_notice('add_submenu_item was deprecated by elgg_add_submenu_item', 1.8);

	$item = array(
		'text' => $label,
		'href' => $link,
		'selected' => $selected
	);

	if (!$group) {
		$group = 'default';
	}

	if ($onclick) {
		$js = "onclick=\"javascript:return confirm('". elgg_echo('deleteconfirm') . "')\"";
		$item['vars'] = array('js' => $js);
	}
	// submenu items were added in the page setup hook usually by checking
	// the context.  We'll pass in the current context here, which will
	// emulate that effect.
	// if context == 'main' (default) it probably means they always wanted
	// the menu item to show up everywhere.
	$context = get_context();

	if ($context == 'main') {
		$context = 'all';
	}
	return elgg_add_submenu_item($item, $context, $group);
}

/**
 * Add an entry to the submenu.
 *
 * @param array $item The item as array(
 * 	'title' => 'Text to display',
 * 	'url' => 'URL of the link',
 * 	'id' => 'entry_unique_id' //used by children items to identify parents
 * 	'parent_id' => 'id_of_parent',
 * 	'selected' => BOOL // Is this item selected? (If NULL or unset will attempt to guess)
 * 	'vars' => array() // Array of vars to pass to the navigation/submenu_item view
 * )
 *
 * @param string $context Context in which to display this menu item.  'all' will make it show up all the time. Use sparingly.
 * @param string $group Group for the item. Each submenu group has its own <ul>
 * @return BOOL
 * @since 1.8
 */
function elgg_add_submenu_item(array $item, $context = 'all', $group = 'default') {
	global $CONFIG;

	if (!isset($CONFIG->submenu_items)) {
		$CONFIG->submenu_items = array();
	}

	if (!isset($CONFIG->submenu_items[$context])) {
		$CONFIG->submenu_items[$context] = array();
	}

	if (!isset($CONFIG->submenu_items[$context][$group])) {
		$CONFIG->submenu_items[$context][$group] = array();
	}

	if (!isset($item['text'])) {
		return FALSE;
	}

	// we use persistent object properties in the submenu
	// setup function, so normalize the array to an object.
	// we pass it in as an array because this would be the only
	// place in elgg that we ask for an object like this.
	// consistency ftw.
	$item_obj = new StdClass();

	foreach ($item as $k => $v) {
		switch ($k) {
			case 'parent_id':
			case 'id':
				// make sure '' and false make sense
				$v = (empty($v)) ? NULL : $v;

			default:
				$item_obj->$k = $v;
				break;
		}
	}

	$CONFIG->submenu_items[$context][$group][] = $item_obj;

	return TRUE;
}

/**
 * Properly nest all submenu entries for contexts $context and 'all'
 *
 * @param string $context
 * @param bool $sort Sort the menu items alphabetically
 * @since 1.8
 */
function elgg_prepare_submenu($context = 'main', $sort = FALSE) {
	global $CONFIG;

	if (!isset($CONFIG->submenu_items) || !($CONFIG->submenu_items)) {
		return FALSE;
	}

	$groups = array();

	if (isset($CONFIG->submenu_items['all'])) {
		$groups = $CONFIG->submenu_items['all'];
	}

	if (isset($CONFIG->submenu_items[$context])) {
		$groups = array_merge_recursive($groups, $CONFIG->submenu_items[$context]);
	}

	if (!$groups) {
		return FALSE;
	}

	foreach ($groups as $group => $items) {
		if ($sort) {
			usort($items, 'elgg_submenu_item_cmp');
		}

		$parsed_menu = array();
		// determin which children need to go in this item.
		foreach ($items as $i => $item) {
			// can only support children if there's an id
			if (isset($item->id)) {
				foreach ($items as $child_i => $child_item) {
					// don't check ourselves or used children.
					if ($child_i == $i || $child_item->used == TRUE) {
						continue;
					}

					if (isset($child_item->parent_id) && $child_item->parent_id == $item->id) {
						if (!isset($item->children)) {
							$item->children = array();
						}
						$item->children[] = $child_item;
						$child_item->parent = $item;
						// don't unset because we still need to check this item for children
						$child_item->used = TRUE;
					}
				}

				// if the parent doesn't have a url, make it the first child item.
				if (isset($item->children) && $item->children && !$item->href) {
					$child = $item->children[0];
					while ($child && !isset($child->href)) {
						if (isset($child->children) && isset($child->children[0])) {
							$child = $child->children[0];
						} else {
							$child = NULL;
						}
					}

					if ($child && isset($child->href)) {
						$item->href = $child->href;
					} else {
						// @todo There are no URLs anywhere in this tree.
						$item->href = $CONFIG->url;
					}
				}
			}

			// only add top-level elements to the menu.
			// the rest are children.
			if (!isset($item->parent_id)) {
				$parsed_menu[] = $item;
			}
		}

		$CONFIG->submenu[$context][$group] = $parsed_menu;
	}

	return TRUE;
}

/**
 * Helper function used to sort submenu items by their display text.
 *
 * @param object $a
 * @param object $b
 * @since 1.8
 */
function elgg_submenu_item_cmp($a, $b) {
	$a = $a->text;
	$b = $b->text;

	return strnatcmp($a, $b);
}

/**
 * Use elgg_get_submenu().
 *
 * @see elgg_get_submenu()
 * @deprecated 1.8
 */
function get_submenu() {
	elgg_deprecated_notice("get_submenu() has been deprecated by elgg_get_submenu()", 1.8);
	return elgg_get_submenu();
}

/**
 * Return the HTML for a sidemenu.
 *
 * @param string $context The context of the submenu (defaults to main)
 * @param BOOL $sort Sort by display name?
 * @return string Formatted HTML.
 * @since 1.8
 */
function elgg_get_submenu($context = NULL, $sort = FALSE) {
	global $CONFIG;

	if (!$context) {
		$context = get_context();
	}

	if (!elgg_prepare_submenu($context, $sort)) {
		return '';
	}

	$groups = $CONFIG->submenu[$context];
	$submenu_html = '';

	foreach ($groups as $group => $items) {
		// how far down we are in children arrays
		$depth = 0;
		// push and pop parent items
		$temp_items = array();

		while ($item = current($items)) {
			$t = '';
			// ignore parents created by a child but parent never defined properly
			if (!isset($item->text) || !($item->text)) {
				next($items);
				continue;
			}

			// try to guess if this should be selected if they don't specify
			if ((!isset($item->selected) || $item->selected === NULL) && isset($item->href)) {
				$item->selected = elgg_http_url_is_identical(full_url(), $item->href);
			}

			// traverse up the parent tree if matached to mark all parents as selected/expanded.
			if ($item->selected && isset($item->parent)) {
				$parent = $item->parent;
				while ($parent) {
					$parent->selected = TRUE;
					if (isset($parent->parent)) {
						$parent = $parent->parent;
					} else {
						$parent = NULL;
					}
				}
			}

			// get the next item
			if (isset($item->children) && $item->children) {
				$depth++;
				array_push($temp_items, $items);
				$items = $item->children;
			} elseif ($depth > 0) {
				// check if there are more children elements in the current items
				// pop back up to the parent(s) if not
				if ($item = next($items)) {
					continue;
				} else {
					while($depth > 0) {
						$depth--;
						$items = array_pop($temp_items);
						if ($item = next($items)) {
							break;
						}
					}
				}
			} else {
				next($items);
			}
		}

		$submenu_html .= elgg_view('navigation/submenu_group', array('group' => $group, 'items' => $items));
	}

	// include the JS for the expand menus too
	return elgg_view('navigation/submenu_js') . $submenu_html;
}

/**
 * Automatically views likes and a like input relating to the given entity
 *
 * @param ElggEntity $entity The entity to like
 * @return string|false The HTML (etc) for the likes, or false on failure
 * @since 1.8
 */
function elgg_view_likes($entity){
	if (!($entity instanceof ElggEntity)) {
		return false;
	}
	if ($likes = trigger_plugin_hook('likes', $entity->getType(), array('entity' => $entity), false)) {
		return $likes;
	} else {
		//display the form
		$likes = elgg_view('likes/forms/edit', array('entity' => $entity));
		return $likes;
	}
}

/**
 * Count the number of likes attached to an entity
 *
 * @param ElggEntity $entity
 * @return int Number of likes
 * @since 1.8
 */
function elgg_count_likes($entity) {
	if ($likeno = trigger_plugin_hook('likes:count', $entity->getType(),
		array('entity' => $entity), false)) {
		return $likeno;
	} else {
		return count_annotations($entity->getGUID(), "", "", "likes");
	}
}

/**
 * Automatically views comments and a comment form relating to the given entity
 *
 * @param ElggEntity $entity The entity to comment on
 * @return string|false The HTML (etc) for the comments, or false on failure
 */
function elgg_view_comments($entity){

	if (!($entity instanceof ElggEntity)) {
		return false;
	}

	if ($comments = trigger_plugin_hook('comments',$entity->getType(),array('entity' => $entity),false)) {
		return $comments;
	} else {
		$comments = list_annotations($entity->getGUID(),'generic_comment');

		//display the comment form
		$comments .= elgg_view('comments/forms/edit',array('entity' => $entity));

		return $comments;
	}
}

/**
 * Count the number of comments attached to an entity
 *
 * @param ElggEntity $entity
 * @return int Number of comments
 */
function elgg_count_comments($entity) {
	if ($commentno = trigger_plugin_hook('comments:count', $entity->getType(),
		array('entity' => $entity), false)) {
		return $commentno;
	} else {
		return count_annotations($entity->getGUID(), "", "", "generic_comment");
	}
}

/**
 * Wrapper function to display search listings.
 *
 * @param string $icon The icon for the listing
 * @param string $info Any information that needs to be displayed.
 * @return string The HTML (etc) representing the listing
 */
function elgg_view_listing($icon, $info) {
	return elgg_view('entities/entity_listing',array('icon' => $icon, 'info' => $info));
}

/**
 * Sets an alternative function to handle templates, which will be passed to by elgg_view.
 * This function must take the $view and $vars parameters from elgg_view:
 *
 * 		function my_template_function(string $view, array $vars = array())
 *
 * @see elgg_view
 *
 * @param string $function_name The name of the function to pass to.
 * @return true|false
 */
function set_template_handler($function_name) {
	global $CONFIG;
	if (!empty($function_name) && is_callable($function_name)) {
		$CONFIG->template_handler = $function_name;
		return true;
	}
	return false;
}

/**
 * Extends a view.
 *
 * The addititional views are displayed before or after the primary view.
 * Priorities less than 500 are displayed before the primary view and
 * greater than 500 after. The default priority is 501.
 *
 * @param string $view The view to extend.
 * @param string $view_extension This view is added to $view
 * @param int $priority The priority, from 0 to 1000, to add at (lowest numbers displayed first)
 * @param string $viewtype Not used
 */
function elgg_extend_view($view, $view_extension, $priority = 501, $viewtype = '') {
	global $CONFIG;

	if (!isset($CONFIG->views)) {
		$CONFIG->views = new stdClass;
	}

	if (!isset($CONFIG->views->extensions)) {
		$CONFIG->views->extensions = array();
	}

	if (!isset($CONFIG->views->extensions[$view])) {
		$CONFIG->views->extensions[$view][500] = "{$view}";
	}

	while(isset($CONFIG->views->extensions[$view][$priority])) {
		$priority++;
	}

	$CONFIG->views->extensions[$view][$priority] = "{$view_extension}";
	ksort($CONFIG->views->extensions[$view]);
}

/**
 * Unextends a view.
 *
 * @param string $view The view that was extended.
 * @param string $view_extension This view that was added to $view
 * @return bool
 * @since 1.7.2
 */
function elgg_unextend_view($view, $view_extension) {
	global $CONFIG;

	if (!isset($CONFIG->views)) {
		return FALSE;
	}

	if (!isset($CONFIG->views->extensions)) {
		return FALSE;
	}

	if (!isset($CONFIG->views->extensions[$view])) {
		return FALSE;
	}

	$priority = array_search($view_extension, $CONFIG->views->extensions[$view]);
	if ($priority === FALSE) {
		return FALSE;
	}

	unset($CONFIG->views->extensions[$view][$priority]);

	return TRUE;
}

/**
 * @deprecated 1.7.  Use elgg_extend_view().
 * @param $view
 * @param $view_name
 * @param $priority
 * @param $viewtype
 */
function extend_view($view, $view_name, $priority = 501, $viewtype = '') {
	elgg_deprecated_notice('extend_view() was deprecated by elgg_extend_view()!', 1.7);
	elgg_extend_view($view, $view_name, $priority, $viewtype);
}

/**
 * Set an alternative base location for a view (as opposed to the default of $CONFIG->viewpath)
 *
 * @param string $view The name of the view
 * @param string $location The base location path
 */
function set_view_location($view, $location, $viewtype = '') {
	global $CONFIG;

	if (empty($viewtype)) {
		$viewtype = 'default';
	}

	if (!isset($CONFIG->views)) {
		$CONFIG->views = new stdClass;
	}

	if (!isset($CONFIG->views->locations)) {
		$CONFIG->views->locations = array($viewtype => array($view => $location));

	} else if (!isset($CONFIG->views->locations[$viewtype])) {
		$CONFIG->views->locations[$viewtype] = array($view => $location);

	} else {
		$CONFIG->views->locations[$viewtype][$view] = $location;
	}
}

/**
 * Auto-registers views from a particular starting location
 *
 * @param string $view_base The base of the view name
 * @param string $folder The folder to begin looking in
 * @param string $base_location_path The base views directory to use with set_view_location
 * @param string $viewtype The type of view we're looking at (default, rss, etc)
 */
function autoregister_views($view_base, $folder, $base_location_path, $viewtype) {
	if (!isset($i)) {
		$i = 0;
	}

	if ($handle = opendir($folder)) {
		while ($view = readdir($handle)) {
			if (!in_array($view,array('.','..','.svn','CVS')) && !is_dir($folder . "/" . $view)) {
				if ((substr_count($view,".php") > 0) || (substr_count($view,".png") > 0)) {
					if (!empty($view_base)) {
						$view_base_new = $view_base . "/";
					} else {
						$view_base_new = "";
					}

					set_view_location($view_base_new . str_replace(".php","",$view), $base_location_path, $viewtype);
				}
			} else if (!in_array($view,array('.','..','.svn','CVS')) && is_dir($folder . "/" . $view)) {
				if (!empty($view_base)) {
					$view_base_new = $view_base . "/";
				} else {
					$view_base_new = "";
				}
				autoregister_views($view_base_new . $view, $folder . "/" . $view, $base_location_path, $viewtype);
			}
		}
	}
}

/**
 * Returns a representation of a full 'page' (which might be an HTML page, RSS file, etc, depending on the current view)
 *
 * @param unknown_type $title
 * @param unknown_type $body
 * @return unknown
 */
function page_draw($title, $body, $sidebar = "") {

	// get messages - try for errors first
	$sysmessages = system_messages(null, "errors");
	if (count($sysmessages["errors"]) == 0) {
		// no errors so grab rest of messages
		$sysmessages = system_messages(null, "");
	} else {
		// we have errors - clear out remaining messages
		system_messages(null, "");
	}

	// Draw the page
	$output = elgg_view('page_shells/default', array(
		'title' => $title,
		'body' => $body,
		'sidebar' => $sidebar,
		'sysmessages' => $sysmessages,
		)
	);

	$split_output = str_split($output, 1024);

	foreach($split_output as $chunk) {
		echo $chunk;
	}
}

/**
 * Library loading and handling
 */

/**
 * @deprecated 1.7
 */
function get_library_files($directory, $exceptions = array(), $list = array()) {
	elgg_deprecated_notice('get_library_files() deprecated by elgg_get_file_list()', 1.7);
	return elgg_get_file_list($directory, $exceptions, $list, array('.php'));
}

/**
 * Returns a list of files in $directory
 *
 * @param str $directory
 * @param array $exceptions Array of filenames to ignore
 * @param array $list Array of files to append to
 * @param mixed $extensions Array of extensions to allow, NULL for all. (With a dot: array('.php'))
 * @return array of filenames including $directory
 */
function elgg_get_file_list($directory, $exceptions = array(), $list = array(), $extensions = NULL) {
	$directory = sanitise_filepath($directory);
	if ($handle = opendir($directory)) {
		while (($file = readdir($handle)) !== FALSE) {
			if (!is_file($directory . $file) || in_array($file, $exceptions)) {
				continue;
			}

			if (is_array($extensions)) {
				if (in_array(strrchr($file, '.'), $extensions)) {
					$list[] = $directory . $file;
				}
			} else {
				$list[] = $directory . $file;
			}
		}
		closedir($handle);
	}

	return $list;
}

/**
 * Ensures that the installation has all the correct files, that PHP is configured correctly, and so on.
 * Leaves appropriate messages in the error register if not.
 *
 * @return true|false True if everything is ok (or Elgg is fit enough to run); false if not.
 */
function sanitised() {
	$sanitised = true;

	if (!file_exists(dirname(dirname(__FILE__)) . "/settings.php")) {
		// See if we are being asked to save the file
		$save_vars = get_input('db_install_vars');
		$result = "";
		if ($save_vars) {
			$rtn = db_check_settings($save_vars['CONFIG_DBUSER'],
									$save_vars['CONFIG_DBPASS'],
									$save_vars['CONFIG_DBNAME'],
									$save_vars['CONFIG_DBHOST'] );
			if ($rtn == FALSE) {
				register_error(elgg_view("messages/sanitisation/dbsettings_error"));
				register_error(elgg_view("messages/sanitisation/settings",
								array(	'settings.php' => $result,
										'sticky' => $save_vars)));
				return FALSE;
			}

			$result = create_settings($save_vars, dirname(dirname(__FILE__)) . "/settings.example.php");


			if (file_put_contents(dirname(dirname(__FILE__)) . "/settings.php", $result)) {
				// blank result to stop it being displayed in textarea
				$result = "";
			}
		}

		// Recheck to see if the file is still missing
		if (!file_exists(dirname(dirname(__FILE__)) . "/settings.php")) {
			register_error(elgg_view("messages/sanitisation/settings", array('settings.php' => $result)));
			$sanitised = false;
		}
	}

	if (!file_exists(dirname(dirname(dirname(__FILE__))) . "/.htaccess")) {
		if (!@copy(dirname(dirname(dirname(__FILE__))) . "/htaccess_dist", dirname(dirname(dirname(__FILE__))) . "/.htaccess")) {
			register_error(elgg_view("messages/sanitisation/htaccess", array('.htaccess' => file_get_contents(dirname(dirname(dirname(__FILE__))) . "/htaccess_dist"))));
			$sanitised = false;
		}
	}

	return $sanitised;
}

/**
 * Registers
 */

/**
 * Adds an array with a name to a given generic array register.
 * For example, these are used for menus.
 *
 * @param string $register_name The name of the top-level register
 * @param string $subregister_name The name of the subregister
 * @param mixed $subregister_value The value of the subregister
 * @param array $children_array Optionally, an array of children
 * @return true|false Depending on success
 */
function add_to_register($register_name, $subregister_name, $subregister_value, $children_array = array()) {
	global $CONFIG;

	if (empty($register_name) || empty($subregister_name)) {
		return false;
	}

	if (!isset($CONFIG->registers)) {
		$CONFIG->registers = array();
	}

	if (!isset($CONFIG->registers[$register_name])) {
		$CONFIG->registers[$register_name]  = array();
	}

	$subregister = new stdClass;
	$subregister->name = $subregister_name;
	$subregister->value = $subregister_value;

	if (is_array($children_array)) {
		$subregister->children = $children_array;
	}

	$CONFIG->registers[$register_name][$subregister_name] = $subregister;
	return true;
}

/**
 * Returns a register object
 *
 * @param string $register_name The name of the register
 * @param mixed $register_value The value of the register
 * @param array $children_array Optionally, an array of children
 * @return false|stdClass Depending on success
 */
function make_register_object($register_name, $register_value, $children_array = array()) {
	elgg_deprecated_notice('make_register_object() is deprecated by add_submenu_item()', 1.7);
	if (empty($register_name) || empty($register_value)) {
		return false;
	}

	$register = new stdClass;
	$register->name = $register_name;
	$register->value = $register_value;
	$register->children = $children_array;

	return $register;
}

/**
 * If it exists, returns a particular register as an array
 *
 * @param string $register_name The name of the register
 * @return array|false Depending on success
 */
function get_register($register_name) {
	global $CONFIG;

	if (isset($CONFIG->registers[$register_name])) {
		return $CONFIG->registers[$register_name];
	}

	return false;
}

/**
 * Adds an item to the menu register
 * This is used in the core to create the tools dropdown menu
 * You can obtain the menu array by calling get_register('menu')
 *
 * @param string $menu_name The name of the menu item
 * @param string $menu_url The URL of the page
 * @param array $menu_children Optionally, an array of submenu items (not currently used)
 * @param string $context
 * @return true|false Depending on success
 */
function add_menu($menu_name, $menu_url, $menu_children = array(), $context = "") {
	global $CONFIG;

	if (!isset($CONFIG->menucontexts)) {
		$CONFIG->menucontexts = array();
	}

	if (empty($context)) {
		$context = get_plugin_name();
	}

	$value = new stdClass();
	$value->url = $menu_url;
	$value->context = $context;

	$CONFIG->menucontexts[] = $context;
	return add_to_register('menu', $menu_name, $value, $menu_children);
}

/**
 * Returns a menu item for use in the children section of add_menu()
 * This is not currently used in the Elgg core
 *
 * @param string $menu_name The name of the menu item
 * @param string $menu_url Its URL
 * @return stdClass|false Depending on success
 */
function menu_item($menu_name, $menu_url) {
	elgg_deprecated_notice('menu_item() is deprecated by add_submenu_item', 1.7);
	return make_register_object($menu_name, $menu_url);
}


/**
 * Message register handling
 * If a null $message parameter is given, the function returns the array of messages so far and empties it
 * based on the $register parameters. Otherwise, any message or array of messages is added.
 *
 * @param string|array $message Optionally, a single message or array of messages to add, (default: null)
 * @param string $register This allows for different types of messages: "errors", "messages" (default: messages)
 * @param bool $count Count the number of messages (default: false)
 * @return true|false|array Either the array of messages, or a response regarding whether the message addition was successful
 */

function system_messages($message = null, $register = "messages", $count = false) {
	if (!isset($_SESSION['msg'])) {
		$_SESSION['msg'] = array();
	}
	if (!isset($_SESSION['msg'][$register]) && !empty($register)) {
		$_SESSION['msg'][$register] = array();
	}
	if (!$count) {
		if (!empty($message) && is_array($message)) {
			$_SESSION['msg'][$register] = array_merge($_SESSION['msg'][$register], $message);
			return true;
		} else if (!empty($message) && is_string($message)) {
			$_SESSION['msg'][$register][] = $message;
			return true;
		} else if (is_null($message)) {
			if ($register != "") {
				$returnarray = array();
				$returnarray[$register] = $_SESSION['msg'][$register];
				$_SESSION['msg'][$register] = array();
			} else {
				$returnarray = $_SESSION['msg'];
				$_SESSION['msg'] = array();
			}
			return $returnarray;
		}
	} else {
		if (!empty($register)) {
			return sizeof($_SESSION['msg'][$register]);
		} else {
			$count = 0;
			foreach($_SESSION['msg'] as $register => $submessages) {
				$count += sizeof($submessages);
			}
			return $count;
		}
	}
	return false;
}

/**
 * Counts the number of messages, either globally or in a particular register
 *
 * @param string $register Optionally, the register
 * @return integer The number of messages
 */
function count_messages($register = "") {
	return system_messages(null,$register,true);
}

/**
 * An alias for system_messages($message) to handle standard user information messages
 *
 * @param string|array $message Message or messages to add
 * @return true|false Success response
 */
function system_message($message) {
	return system_messages($message, "messages");
}

/**
 * An alias for system_messages($message) to handle error messages
 *
 * @param string|array $message Error or errors to add
 * @return true|false Success response
 */
function register_error($error) {
	return system_messages($error, "errors");
}

/**
 * Event register
 * Adds functions to the register for a particular event, but also calls all functions registered to an event when required
 *
 * Event handler functions must be of the form:
 *
 * 		event_handler_function($event, $object_type, $object);
 *
 * And must return true or false depending on success.  A false will halt the event in its tracks and no more functions will be called.
 *
 * You can then simply register them using the following function. Optionally, this can be called with a priority nominally from 0 to 1000, where functions with lower priority values are called first (note that priorities CANNOT be negative):
 *
 * 		register_elgg_event_handler($event, $object_type, $function_name [, $priority = 500]);
 *
 * Note that you can also use 'all' in place of both the event and object type.
 *
 * To trigger an event properly, you should always use:
 *
 * 		trigger_elgg_event($event, $object_type [, $object]);
 *
 * Where $object is optional, and represents the $object_type the event concerns. This will return true if successful, or false if it fails.
 *
 * @param string $event The type of event (eg 'init', 'update', 'delete')
 * @param string $object_type The type of object (eg 'system', 'blog', 'user')
 * @param string $function The name of the function that will handle the event
 * @param int $priority A priority to add new event handlers at. Lower numbers will be called first (default 500)
 * @param boolean $call Set to true to call the event rather than add to it (default false)
 * @param mixed $object Optionally, the object the event is being performed on (eg a user)
 * @return true|false Depending on success
 */
function events($event = "", $object_type = "", $function = "", $priority = 500, $call = false, $object = null) {
	global $CONFIG;

	if (!isset($CONFIG->events)) {
		$CONFIG->events = array();
	} else if (!isset($CONFIG->events[$event]) && !empty($event)) {
		$CONFIG->events[$event] = array();
	} else if (!isset($CONFIG->events[$event][$object_type]) && !empty($event) && !empty($object_type)) {
		$CONFIG->events[$event][$object_type] = array();
	}

	if (!$call) {
		if (!empty($event) && !empty($object_type) && is_callable($function)) {
			$priority = (int) $priority;
			if ($priority < 0) {
				$priority = 0;
			}
			while (isset($CONFIG->events[$event][$object_type][$priority])) {
				$priority++;
			}
			$CONFIG->events[$event][$object_type][$priority] = $function;
			ksort($CONFIG->events[$event][$object_type]);
			return true;
		} else {
			return false;
		}
	} else {
		$return = true;
		if (!empty($CONFIG->events[$event][$object_type]) && is_array($CONFIG->events[$event][$object_type])) {
			foreach($CONFIG->events[$event][$object_type] as $eventfunction) {
				if ($eventfunction($event, $object_type, $object) === false) {
					return false;
				}
			}
		}

		if (!empty($CONFIG->events['all'][$object_type]) && is_array($CONFIG->events['all'][$object_type])) {
			foreach($CONFIG->events['all'][$object_type] as $eventfunction) {
				if ($eventfunction($event, $object_type, $object) === false) {
					return false;
				}
			}
		}

		if (!empty($CONFIG->events[$event]['all']) && is_array($CONFIG->events[$event]['all'])) {
			foreach($CONFIG->events[$event]['all'] as $eventfunction) {
				if ($eventfunction($event, $object_type, $object) === false) {
					return false;
				}
			}
		}

		if (!empty($CONFIG->events['all']['all']) && is_array($CONFIG->events['all']['all'])) {
			foreach($CONFIG->events['all']['all'] as $eventfunction) {
				if ($eventfunction($event, $object_type, $object) === false) {
					return false;
				}
			}
		}

		return $return;

	}

	return false;
}

/**
 * Alias function for events, that registers a function to a particular kind of event
 *
 * @param string $event The event type
 * @param string $object_type The object type
 * @param string $function The function name
 * @return true|false Depending on success
 */
function register_elgg_event_handler($event, $object_type, $function, $priority = 500) {
	return events($event, $object_type, $function, $priority);
}

/**
 * Unregisters a function to a particular kind of event
 *
 * @param string $event The event type
 * @param string $object_type The object type
 * @param string $function The function name
 */
function unregister_elgg_event_handler($event, $object_type, $function) {
	global $CONFIG;
	foreach($CONFIG->events[$event][$object_type] as $key => $event_function) {
		if ($event_function == $function) {
			unset($CONFIG->events[$event][$object_type][$key]);
		}
	}
}

/**
 * Alias function for events, that triggers a particular kind of event
 *
 * @param string $event The event type
 * @param string $object_type The object type
 * @param string $function The function name
 * @return true|false Depending on success
 */
function trigger_elgg_event($event, $object_type, $object = null) {
	$return = true;
	$return1 = events($event, $object_type, "", null, true, $object);
	if (!is_null($return1)) {
		$return = $return1;
	}
	return $return;
}

/**
 * Register a function to a plugin hook for a particular hook name and type, with a given priority.
 *
 * eg if you want the function "export_user" to be called when the hook "export" for "user" entities
 * is run, use:
 *
 * 		register_plugin_hook("export", "user", "export_user");
 *
 * "all" is a valid value for both $hook and $entity_type. "none" is a valid value for $entity_type.
 *
 * The export_user function would then be defined as:
 *
 * 		function export_user($hook, $entity_type, $returnvalue, $params);
 *
 * Where $returnvalue is the return value returned by the last function returned by the hook, and
 * $params is an array containing a set of parameters (or nothing).
 *
 * @param string $hook The name of the hook
 * @param string $type The type of the hook (NB Can be an ElggEntity type [user, object, group, site] or custom-defined 'get_sections')
 * @param string $function The name of a valid function to be run
 * @param string $priority The priority - 0 is first, 1000 last, default is 500
 * @return true|false Depending on success
 */
function register_plugin_hook($hook, $type, $function, $priority = 500) {
	global $CONFIG;

	if (!isset($CONFIG->hooks)) {
		$CONFIG->hooks = array();
	} else if (!isset($CONFIG->hooks[$hook]) && !empty($hook)) {
		$CONFIG->hooks[$hook] = array();
	} else if (!isset($CONFIG->hooks[$hook][$type]) && !empty($type)) {
		$CONFIG->hooks[$hook][$type] = array();
	}

	if (!empty($hook) && !empty($type) && is_callable($function)) {
		$priority = (int) $priority;
		if ($priority < 0) {
			$priority = 0;
		}
		while (isset($CONFIG->hooks[$hook][$type][$priority])) {
			$priority++;
		}
		$CONFIG->hooks[$hook][$type][$priority] = $function;
		ksort($CONFIG->hooks[$hook][$type]);
		return true;
	} else {
		return false;
	}
}

/**
 * Unregister a function to a plugin hook for a particular entity type
 *
 * @param string $hook The name of the hook
 * @param string $entity_type The name of the type of entity (eg "user", "object" etc)
 * @param string $function The name of a valid function to be run
 */
function unregister_plugin_hook($hook, $entity_type, $function) {
	global $CONFIG;
	foreach($CONFIG->hooks[$hook][$entity_type] as $key => $hook_function) {
		if ($hook_function == $function) {
			unset($CONFIG->hooks[$hook][$entity_type][$key]);
		}
	}
}

/**
 * Triggers a plugin hook, with various parameters as an array. For example, to provide
 * a 'foo' hook that concerns the type 'bar', with a parameter called 'param1'
 * with value 'value1', that by default returns true, you'd call:
 *
 * trigger_plugin_hook('foo', 'bar', array('param1' => 'value1'), true);
 *
 * @see register_plugin_hook
 * @param string $hook The name of the hook to trigger (NB: "all" will trigger for all $types regardless of $hook value)
 * @param string $type The type of the hook to trigger (NB: "all" will trigger for all $hooks regardless of $type value)
 * @param array $params Any parameters. It's good practice to name the keys, i.e. by using array('name' => 'value', 'name2' => 'value2')
 * @param mixed $returnvalue An initial return value
 * @return mixed|null The cumulative return value for the plugin hook functions
 */
function trigger_plugin_hook($hook, $type, $params = null, $returnvalue = null) {
	global $CONFIG;

	if (!empty($CONFIG->hooks[$hook][$type]) && is_array($CONFIG->hooks[$hook][$type])) {
		foreach($CONFIG->hooks[$hook][$type] as $hookfunction) {
			$temp_return_value = $hookfunction($hook, $type, $returnvalue, $params);
			if (!is_null($temp_return_value)) {
				$returnvalue = $temp_return_value;
			}
		}
	}

	if (!empty($CONFIG->hooks['all'][$type]) && is_array($CONFIG->hooks['all'][$type])) {
		foreach($CONFIG->hooks['all'][$type] as $hookfunction) {
			$temp_return_value = $hookfunction($hook, $type, $returnvalue, $params);
			if (!is_null($temp_return_value)) $returnvalue = $temp_return_value;
		}
	}

	if (!empty($CONFIG->hooks[$hook]['all']) && is_array($CONFIG->hooks[$hook]['all'])) {
		foreach($CONFIG->hooks[$hook]['all'] as $hookfunction) {
			$temp_return_value = $hookfunction($hook, $type, $returnvalue, $params);
			if (!is_null($temp_return_value)) {
				$returnvalue = $temp_return_value;
			}
		}
	}

	if (!empty($CONFIG->hooks['all']['all']) && is_array($CONFIG->hooks['all']['all'])) {
		foreach($CONFIG->hooks['all']['all'] as $hookfunction) {
			$temp_return_value = $hookfunction($hook, $type, $returnvalue, $params);
			if (!is_null($temp_return_value)) {
				$returnvalue = $temp_return_value;
			}
		}
	}

	return $returnvalue;
}

/**
 * Error handling
 */

/**
 * PHP Error handler function.
 * This function acts as a wrapper to catch and report PHP error messages.
 *
 * @see http://www.php.net/set-error-handler
 * @param int $errno The level of the error raised
 * @param string $errmsg The error message
 * @param string $filename The filename the error was raised in
 * @param int $linenum The line number the error was raised at
 * @param array $vars An array that points to the active symbol table at the point that the error occurred
 */
function __elgg_php_error_handler($errno, $errmsg, $filename, $linenum, $vars) {
	$error = date("Y-m-d H:i:s (T)") . ": \"$errmsg\" in file $filename (line $linenum)";

	switch ($errno) {
		case E_USER_ERROR:
			error_log("ERROR: $error");
			register_error("ERROR: $error");

			// Since this is a fatal error, we want to stop any further execution but do so gracefully.
			throw new Exception($error);
			break;

		case E_WARNING :
		case E_USER_WARNING :
			error_log("WARNING: $error");
			break;

		default:
			global $CONFIG;
			if (isset($CONFIG->debug) && $CONFIG->debug === 'NOTICE') {
				error_log("NOTICE: $error");
			}
	}

	return true;
}

/**
 * Throws a message to the Elgg logger
 *
 * The Elgg log is currently implemented such that any messages sent at a level
 * greater than or equal to the debug setting will be sent to elgg_dump.
 * The default location for elgg_dump is the screen except for notices.
 *
 * Note: No messages will be displayed unless debugging has been enabled.
 *
 * @param str $message User message
 * @param str $level NOTICE | WARNING | ERROR | DEBUG
 * @return bool
 */
function elgg_log($message, $level='NOTICE') {
	global $CONFIG;

	// only log when debugging is enabled
	if (isset($CONFIG->debug)) {
		// debug to screen or log?
		$to_screen = !($CONFIG->debug == 'NOTICE');

		switch ($level) {
			case 'ERROR':
				// always report
				elgg_dump("$level: $message", $to_screen, $level);
				break;
			case 'WARNING':
			case 'DEBUG':
				// report except if user wants only errors
				if ($CONFIG->debug != 'ERROR') {
					elgg_dump("$level: $message", $to_screen, $level);
				}
				break;
			case 'NOTICE':
			default:
				// only report when lowest level is desired
				if ($CONFIG->debug == 'NOTICE') {
					elgg_dump("$level: $message", FALSE, $level);
				}
				break;
		}

		return TRUE;
	}

	return FALSE;
}

/**
 * Extremely generic var_dump-esque wrapper
 *
 * Immediately dumps the given $value as a human-readable string.
 * The $value can instead be written to the screen or server log depending on
 * the value of the $to_screen flag.
 *
 * @param mixed $value
 * @param bool $to_screen
 * @param string $level
 * @return void
 */
function elgg_dump($value, $to_screen = TRUE, $level = 'NOTICE') {
	global $CONFIG;

	// plugin can return false to stop the default logging method
	$params = array('level' => $level,
					'msg' => $value,
					'to_screen' => $to_screen);
	if (!trigger_plugin_hook('debug', 'log', $params, true)) {
		return;
	}

	// Do not want to write to screen before page creation has started.
	// This is not fool-proof but probably fixes 95% of the cases when logging
	// results in data sent to the browser before the page is begun.
	if (!isset($CONFIG->pagesetupdone)) {
		$to_screen = FALSE;
	}

	if ($to_screen == TRUE) {
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	} else {
		error_log(print_r($value, TRUE));
	}
}

/**
 * Custom exception handler.
 * This function catches any thrown exceptions and handles them appropriately.
 *
 * @see http://www.php.net/set-exception-handler
 * @param Exception $exception The exception being handled
 */
function __elgg_php_exception_handler($exception) {
	error_log("*** FATAL EXCEPTION *** : " . $exception);

	ob_end_clean(); // Wipe any existing output buffer

	// make sure the error isn't cached
	header("Cache-Control: no-cache, must-revalidate", true);
	header('Expires: Fri, 05 Feb 1982 00:00:00 -0500', true);
	//header("Internal Server Error", true, 500);

	$body = elgg_view("messages/exceptions/exception",array('object' => $exception));
	page_draw(elgg_echo('exception:title'), $body);
}

/**
 * Data lists
 */

$DATALIST_CACHE = array();

/**
 * Get the value of a particular piece of data in the datalist
 *
 * @param string $name The name of the datalist
 * @return string|false Depending on success
 */
function datalist_get($name) {
	global $CONFIG, $DATALIST_CACHE;

	// We need this, because sometimes datalists are received before the database is created
	if (!is_db_installed()) {
		return false;
	}

	$name = sanitise_string($name);
	if (isset($DATALIST_CACHE[$name])) {
		return $DATALIST_CACHE[$name];
	}

	// If memcache enabled then cache value in memcache
	$value = null;
	static $datalist_memcache;
	if ((!$datalist_memcache) && (is_memcache_available())) {
		$datalist_memcache = new ElggMemcache('datalist_memcache');
	}
	if ($datalist_memcache) {
		$value = $datalist_memcache->load($name);
	}
	if ($value) {
		return $value;
	}

	// [Marcus Povey 20090217 : Now retrieving all datalist values on first load as this saves about 9 queries per page]
	$result = get_data("SELECT * from {$CONFIG->dbprefix}datalists");
	if ($result) {
		foreach ($result as $row) {
			$DATALIST_CACHE[$row->name] = $row->value;

			// Cache it if memcache is available
			if ($datalist_memcache) {
				$datalist_memcache->save($row->name, $row->value);
			}
		}

		if (isset($DATALIST_CACHE[$name])) {
			return $DATALIST_CACHE[$name];
		}
	}


	/*if ($row = get_data_row("SELECT value from {$CONFIG->dbprefix}datalists where name = '{$name}' limit 1")) {
		$DATALIST_CACHE[$name] = $row->value;

		// Cache it if memcache is available
		if ($datalist_memcache) $datalist_memcache->save($name, $row->value);

		return $row->value;
	}*/

	return false;
}

/**
 * Sets the value for a system-wide piece of data (overwriting a previous value if it exists)
 *
 * @param string $name The name of the datalist
 * @param string $value The new value
 * @return true
 */
function datalist_set($name, $value) {

	global $CONFIG, $DATALIST_CACHE;

	$name = sanitise_string($name);
	$value = sanitise_string($value);

	// If memcache is available then invalidate the cached copy
	static $datalist_memcache;
	if ((!$datalist_memcache) && (is_memcache_available())) {
		$datalist_memcache = new ElggMemcache('datalist_memcache');
	}

	if ($datalist_memcache) {
		$datalist_memcache->delete($name);
	}

	//delete_data("delete from {$CONFIG->dbprefix}datalists where name = '{$name}'");
	insert_data("INSERT into {$CONFIG->dbprefix}datalists set name = '{$name}', value = '{$value}' ON DUPLICATE KEY UPDATE value='{$value}'");

	$DATALIST_CACHE[$name] = $value;

	return true;
}

/**
 * Runs a function once - not per page load, but per installation.
 * If you like, you can also set the threshold for the function execution - i.e.,
 * if the function was executed before or on $timelastupdatedcheck, this
 * function will run it again.
 *
 * @param string $functionname The name of the function you want to run.
 * @param int $timelastupdatedcheck Optionally, the UNIX epoch timestamp of the execution threshold
 * @return true|false Depending on success.
 */
function run_function_once($functionname, $timelastupdatedcheck = 0) {
	if ($lastupdated = datalist_get($functionname)) {
		$lastupdated = (int) $lastupdated;
	} else {
		$lastupdated = 0;
	}
	if (is_callable($functionname) && $lastupdated <= $timelastupdatedcheck) {
		$functionname();
		datalist_set($functionname,time());
		return true;
	} else {
		return false;
	}
}

/**
 * Sends a notice about deprecated use of a function, view, etc.
 * Note: This will ALWAYS at least log a warning.  Don't use to pre-deprecate things.
 * This assumes we are releasing in order and deprecating according to policy.
 *
 * @param str $msg Message to log / display.
 * @param str $version human-readable *release* version the function was deprecated. No bloody A, B, (R)C, or D.
 *
 * @return bool
 */
function elgg_deprecated_notice($msg, $dep_version) {
	// if it's a major release behind, visual and logged
	// if it's a 2 minor releases behind, visual and logged
	// if it's 1 minor release behind, logged.
	// bugfixes don't matter because you're not deprecating between them, RIGHT?

	if (!$dep_version) {
		return FALSE;
	}

	$elgg_version = get_version(TRUE);
	$elgg_version_arr = explode('.', $elgg_version);
	$elgg_major_version = $elgg_version_arr[0];
	$elgg_minor_version = $elgg_version_arr[1];

	$dep_version_arr = explode('.', $dep_version);
	$dep_major_version = $dep_version_arr[0];
	$dep_minor_version = $dep_version_arr[1];

	$last_working_version = $dep_minor_version - 1;

	$visual = FALSE;

	// use version_compare to account for 1.7a < 1.7
	if (($dep_major_version < $elgg_major_version)
	|| (($elgg_minor_version - $last_working_version) > 1)) {
		$visual = TRUE;
	}

	$msg = "Deprecated in $dep_version: $msg";

	if ($visual) {
		register_error($msg);
	}

	// Get a file and line number for the log. Never show this in the UI.
	// Skip over the function that sent this notice and see who called the deprecated
	// function itself.
	$backtrace = debug_backtrace();
	$caller = $backtrace[1];
	$msg .= " (Called from {$caller['file']}:{$caller['line']})";

	elgg_log($msg, 'WARNING');

	return TRUE;
}


/**
 * Privilege elevation and gatekeeper code
 */


/**
 * Gatekeeper function which ensures that a we are being executed from
 * a specified location.
 *
 * To use, call this function with the function name (and optional file location) that it has to be called
 * from, it will either return true or false.
 *
 * e.g.
 *
 * function my_secure_function()
 * {
 * 		if (!call_gatekeeper("my_call_function"))
 * 			return false;
 *
 * 		... do secure stuff ...
 * }
 *
 * function my_call_function()
 * {
 * 		// will work
 * 		my_secure_function();
 * }
 *
 * function bad_function()
 * {
 * 		// Will not work
 * 		my_secure_function();
 * }
 *
 * @param mixed $function The function that this function must have in its call stack,
 * 		to test against a method pass an array containing a class and method name.
 * @param string $file Optional file that the function must reside in.
 */
function call_gatekeeper($function, $file = "") {
	// Sanity check
	if (!$function) {
		return false;
	}

	// Check against call stack to see if this is being called from the correct location
	$callstack = debug_backtrace();
	$stack_element = false;

	foreach ($callstack as $call) {
		if (is_array($function)) {
			if (
				(strcmp($call['class'], $function[0]) == 0) &&
				(strcmp($call['function'], $function[1]) == 0)
			) {
				$stack_element = $call;
			}
		} else {
			if (strcmp($call['function'], $function) == 0) {
				$stack_element = $call;
			}
		}
	}

	if (!$stack_element) {
		return false;
	}


	// If file then check that this it is being called from this function
	if ($file) {
		$mirror = null;

		if (is_array($function)) {
			$mirror = new ReflectionMethod($function[0], $function[1]);
		} else {
			$mirror = new ReflectionFunction($function);
		}

		if ((!$mirror) || (strcmp($file,$mirror->getFileName())!=0)) {
			return false;
		}
	}

	return true;
}

/**
 * This function checks to see if it is being called at somepoint by a function defined somewhere
 * on a given path (optionally including subdirectories).
 *
 * This function is similar to call_gatekeeper() but returns true if it is being called by a method or function which has been defined on a given path or by a specified file.
 *
 * @param string $path The full path and filename that this function must have in its call stack If a partial path is given and $include_subdirs is true, then the function will return true if called by any function in or below the specified path.
 * @param bool $include_subdirs Are subdirectories of the path ok, or must you specify an absolute path and filename.
 * @param bool $strict_mode If true then the calling method or function must be directly called by something on $path, if false the whole call stack is searched.
 */
function callpath_gatekeeper($path, $include_subdirs = true, $strict_mode = false) {
	global $CONFIG;

	$path = sanitise_string($path);

	if ($path) {
		$callstack = debug_backtrace();

		foreach ($callstack as $call) {
			$call['file'] = str_replace("\\","/",$call['file']);

			if ($include_subdirs) {
				if (strpos($call['file'], $path) === 0) {

					if ($strict_mode) {
						$callstack[1]['file'] = str_replace("\\","/",$callstack[1]['file']);
						if ($callstack[1] === $call) { return true; }
					} else {
						return true;
					}
				}
			} else {
				if (strcmp($path, $call['file'])==0) {
					if ($strict_mode) {
						if ($callstack[1] === $call) {
							return true;
						}
					} else {
						return true;
					}
				}
			}

		}
		return false;
	}

	if (isset($CONFIG->debug)) {
		system_message("Gatekeeper'd function called from {$callstack[1]['file']}:{$callstack[1]['line']}\n\nStack trace:\n\n" . print_r($callstack, true));
	}

	return false;
}

/**
 * Returns true or false depending on whether a PHP .ini setting is on or off
 *
 * @param string $ini_get_arg The INI setting
 * @return true|false Depending on whether it's on or off
 */
function ini_get_bool($ini_get_arg) {
	$temp = ini_get($ini_get_arg);

	if ($temp == '1' or strtolower($temp) == 'on') {
		return true;
	}
	return false;
}

/**
 * Function to be used in array_filter which returns true if $string is not null.
 *
 * @param string $string
 * @return bool
 */
function is_not_null($string) {
	if (($string==='') || ($string===false) || ($string===null)) {
		return false;
	}

	return true;
}


/**
 * Normalise the singular keys in an options array
 * to the plural keys.
 *
 * @param $options
 * @param $singulars
 * @return array
 */
function elgg_normalise_plural_options_array($options, $singulars) {
	foreach ($singulars as $singular) {
		$plural = $singular . 's';

		// normalize the singular to plural
		// isset() returns FALSE for array values of NULL, so they are ignored.
		// everything else falsy is included.
		//if (isset($options[$singular]) && $options[$singular] !== NULL && $options[$singular] !== FALSE) {
		if (isset($options[$singular])) {
			if (isset($options[$plural])) {
				if (is_array($options[$plural])) {
					$options[$plural][] = $options[$singlar];
				} else {
					$options[$plural] = array($options[$plural], $options[$singular]);
				}
			} else {
				$options[$plural] = array($options[$singular]);
			}
		}
		unset($options[$singular]);
	}

	return $options;
}

/**
 * Get the full URL of the current page.
 *
 * @return string The URL
 */
function full_url() {
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
	$port = ($_SERVER["SERVER_PORT"] == "80" || $_SERVER["SERVER_PORT"] == "443") ? "" : (":".$_SERVER["SERVER_PORT"]);

	$quotes = array('\'', '"');
	$encoded = array('%27', '%22');

	return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . str_replace($quotes, $encoded, $_SERVER['REQUEST_URI']);
}

/**
 * Useful function found in the comments on the PHP man page for ip2long.
 * Returns 1 if an IP matches a given range.
 *
 * TODO: Check licence... assuming this is PD since it was found several places on the interwebs..
 * please check or rewrite.
 *
 * Matches:
 *  xxx.xxx.xxx.xxx        (exact)
 *  xxx.xxx.xxx.[yyy-zzz]  (range)
 *  xxx.xxx.xxx.xxx/nn    (nn = # bits, cisco style -- i.e. /24 = class C)
 * Does not match:
 * xxx.xxx.xxx.xx[yyy-zzz]  (range, partial octets not supported)
 */
function test_ip($range, $ip) {
	$result = 1;

	# IP Pattern Matcher
	# J.Adams <jna@retina.net>
	#
	# Matches:
	#
	# xxx.xxx.xxx.xxx        (exact)
	# xxx.xxx.xxx.[yyy-zzz]  (range)
	# xxx.xxx.xxx.xxx/nn    (nn = # bits, cisco style -- i.e. /24 = class C)
	#
	# Does not match:
	# xxx.xxx.xxx.xx[yyy-zzz]  (range, partial octets not supported)

	if (ereg("([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)/([0-9]+)",$range,$regs)) {
		# perform a mask match
		$ipl = ip2long($ip);
		$rangel = ip2long($regs[1] . "." . $regs[2] . "." . $regs[3] . "." . $regs[4]);

		$maskl = 0;

		for ($i = 0; $i< 31; $i++) {
			if ($i < $regs[5]-1) {
				$maskl = $maskl + pow(2,(30-$i));
			}
		}

		if (($maskl & $rangel) == ($maskl & $ipl)) {
			return 1;
		} else {
			return 0;
		}
	} else {
		# range based
		$maskocts = split("\.",$range);
		$ipocts = split("\.",$ip);

		# perform a range match
		for ($i=0; $i<4; $i++) {
			if (ereg("\[([0-9]+)\-([0-9]+)\]",$maskocts[$i],$regs)) {
				if ( ($ipocts[$i] > $regs[2]) || ($ipocts[$i] < $regs[1])) {
					$result = 0;
				}
			} else {
				if ($maskocts[$i] <> $ipocts[$i]) {
					$result = 0;
				}
			}
		}
	}

	return $result;
}

/**
 * Match an IP address against a number of ip addresses or ranges, returning true if found.
 *
 * @param array $networks
 * @param string $ip
 * @return bool
 */
function is_ip_in_array(array $networks, $ip) {
	global $SYSTEM_LOG;

	foreach ($networks as $network) {
		if (test_ip(trim($network), $ip)) {
			return true;
		}
	}

	return false;
}

/**
 * An interface for objects that behave as elements within a social network that have a profile.
 *
 */
interface Friendable {
	/**
	 * Adds a user as a friend
	 *
	 * @param int $friend_guid The GUID of the user to add
	 */
	public function addFriend($friend_guid);

	/**
	 * Removes a user as a friend
	 *
	 * @param int $friend_guid The GUID of the user to remove
	 */
	public function removeFriend($friend_guid);

	/**
	 * Determines whether or not the current user is a friend of this entity
	 *
	 */
	public function isFriend();

	/**
	 * Determines whether or not this entity is friends with a particular entity
	 *
	 * @param int $user_guid The GUID of the entity this entity may or may not be friends with
	 */
	public function isFriendsWith($user_guid);

	/**
	 * Determines whether or not a foreign entity has made this one a friend
	 *
	 * @param int $user_guid The GUID of the foreign entity
	 */
	public function isFriendOf($user_guid);

	/**
	 * Returns this entity's friends
	 *
	 * @param string $subtype The subtype of entity to return
	 * @param int $limit The number of entities to return
	 * @param int $offset Indexing offset
	 */
	public function getFriends($subtype = "", $limit = 10, $offset = 0);

	/**
	 * Returns entities that have made this entity a friend
	 *
	 * @param string $subtype The subtype of entity to return
	 * @param int $limit The number of entities to return
	 * @param int $offset Indexing offset
	 */
	public function getFriendsOf($subtype = "", $limit = 10, $offset = 0);

	/**
	 * Returns objects in this entity's container
	 *
	 * @param string $subtype The subtype of entity to return
	 * @param int $limit The number of entities to return
	 * @param int $offset Indexing offset
	 */
	public function getObjects($subtype="", $limit = 10, $offset = 0);

	/**
	 * Returns objects in the containers of this entity's friends
	 *
	 * @param string $subtype The subtype of entity to return
	 * @param int $limit The number of entities to return
	 * @param int $offset Indexing offset
	 */
	public function getFriendsObjects($subtype = "", $limit = 10, $offset = 0);

	/**
	 * Returns the number of object entities in this entity's container
	 *
	 * @param string $subtype The subtype of entity to count
	 */
	public function countObjects($subtype = "");
}

/**
 * Rebuilds a parsed (partial) URL
 *
 * @param array $parts Associative array of URL components like parse_url() returns
 * @param bool $htmlencode HTML Encode the url?
 * @return str Full URL
 * @since 1.7
 */
function elgg_http_build_url(array $parts, $html_encode = TRUE) {
	// build only what's given to us.
	$scheme = isset($parts['scheme']) ? "{$parts['scheme']}://" : '';
	$host = isset($parts['host']) ? "{$parts['host']}" : '';
	$port = isset($parts['port']) ? ":{$parts['port']}" : '';
	$path = isset($parts['path']) ? "{$parts['path']}" : '';
	$query = isset($parts['query']) ? "?{$parts['query']}" : '';

	$string = $scheme . $host . $port . $path . $query;

	if ($html_encode) {
		return elgg_format_url($string);
	} else {
		return $string;
	}
}


/**
 * Adds action tokens to URL
 *
 * @param str $link Full action URL
 * @param bool $htmlencode html encode the url?
 * @return str URL with action tokens
 * @since 1.7
 */
function elgg_add_action_tokens_to_url($url, $html_encode = TRUE) {
	$components = parse_url($url);

	if (isset($components['query'])) {
		$query = elgg_parse_str($components['query']);
	} else {
		$query = array();
	}

	if (isset($query['__elgg_ts']) && isset($query['__elgg_token'])) {
		return $url;
	}

	// append action tokens to the existing query
	$query['__elgg_ts'] = time();
	$query['__elgg_token'] = generate_action_token($query['__elgg_ts']);
	$components['query'] = http_build_query($query);

	// rebuild the full url
	return elgg_http_build_url($components, $html_encode);
}

/**
 * @deprecated 1.7 final
 */
function elgg_validate_action_url($url) {
	elgg_deprecated_notice('elgg_validate_action_url had a short life. Use elgg_add_action_tokens_to_url() instead.', '1.7b');

	return elgg_add_action_tokens_to_url($url);
}

/**
 * Removes a single elementry from a (partial) url query.
 *
 * @param string $url
 * @param string $element
 * @return string
 */
function elgg_http_remove_url_query_element($url, $element) {
	$url_array = parse_url($url);

	if (isset($url_array['query'])) {
		$query = elgg_parse_str($url_array['query']);
	} else {
		// nothing to remove. Return original URL.
		return $url;
	}

	if (array_key_exists($element, $query)) {
		unset($query[$element]);
	}

	$url_array['query'] = http_build_query($query);
	$string = elgg_http_build_url($url_array);
	return $string;
}


/**
 * Adds get params to $url
 *
 * @param str $url
 * @param array $elements k/v pairs.
 * @return str
 */
function elgg_http_add_url_query_elements($url, array $elements) {
	$url_array = parse_url($url);

	if (isset($url_array['query'])) {
		$query = elgg_parse_str($url_array['query']);
	} else {
		$query = array();
	}

	foreach ($elements as $k => $v) {
		$query[$k] = $v;
	}

	$url_array['query'] = http_build_query($query);
	$string = elgg_http_build_url($url_array);

	return $string;
}


/**
 * Breadcrumb support.
 */

/**
 * Adds a breadcrumb to the stack
 *
 * @param string $title The title to display
 * @param string $link Optional. The link for the title.
 */
function elgg_push_breadcrumb($title, $link = NULL) {
	global $CONFIG;
	if (!is_array($CONFIG->breadcrumbs)) {
		$CONFIG->breadcrumbs = array();
	}

	// avoid key collisions.
	$CONFIG->breadcrumbs[] = array('title' => $title, 'link' => $link);
}

/**
 * Removes last breadcrumb entry.
 *
 * @return array popped item.
 */
function elgg_pop_breadcrumb() {
	global $CONFIG;

	if (is_array($CONFIG->breadcrumbs)) {
		array_pop($CONFIG->breadcrumbs);
	}

	return FALSE;
}

/**
 * Returns all breadcrumbs
 *
 * @return array Breadcrumbs
 */
function elgg_get_breadcrumbs() {
	global $CONFIG;

	return (is_array($CONFIG->breadcrumbs)) ? $CONFIG->breadcrumbs : array();
}


/**
 * Sticky forms
 */

/**
 * Load all the REQUEST variables into the sticky form cache
 *
 * Call this from an action when you want all your submitted variables
 * available if the submission fails validation and is sent back to the form
 */
function elgg_make_sticky_form($form_name) {
	global $CONFIG;

	$CONFIG->active_sticky_form = $form_name;
	elgg_clear_sticky_form($form_name);

	if (!isset($_SESSION['sticky_forms'])) {
		$_SESSION['sticky_forms'] = array();
	}
	$_SESSION['sticky_forms'][$form_name] = array();

	foreach($_REQUEST as $key => $var) {
		// will go through XSS filtering on the get function
		$_SESSION['sticky_forms'][$form_name][$key] = $var;
	}
}


/**
 * Clear the sticky form cache
 *
 * Call this if validation is successful in the action handler or
 * when they sticky values have been used to repopulate the form
 * after a validation error.
 *
 * @param string $name Form namespace
 */
function elgg_clear_sticky_form($form_name) {
	unset($_SESSION['sticky_forms'][$form_name]);
}

/**
 * Has this form been made sticky
 *
 * @param string $name Form namespace
 * @return boolean
 */
function elgg_is_sticky_form($form_name) {
	return isset($_SESSION['sticky_forms'][$form_name]);
}

/**
 * Get a specific stick variable
 *
 * @param string $variable The name of the variable
 * @param mixed $default Default value if the variable does not exist in sticky cache
 * @param boolean $filter_result Filter for bad input if true
 * @return mixed
 *
 * @todo should this filter the default value?
 */
function elgg_get_sticky_value($form_name, $variable, $default = NULL, $filter_result = true) {
	if (isset($_SESSION['sticky_forms'][$form_name][$variable])) {
		$value = $_SESSION['sticky_forms'][$form_name][$variable];
		if ($filter_result) {
			// XSS filter result
			$value = filter_tags($value);
		}
		return $value;
	}
	return $default;
}

/**
 * Clear a specific sticky variable
 *
 * @param string $variable The name of the variable to clear
 */
function elgg_clear_sticky_value($form_name, $variable) {
	unset($_SESSION['sticky_forms'][$form_name][$variable]);
}

/**
 * Returns the current active sticky form.
 * @return mixed Str | FALSE
 */
function elgg_get_active_sticky_form() {
	global $CONFIG;

	if (isset($CONFIG->active_sticky_form)) {
		$form_name = $CONFIG->active_sticky_form;
	} else {
		return FALSE;
	}

	return (elgg_is_sticky_form($form_name)) ? $form_name : FALSE;
}

function elgg_set_active_sticky_form($form_name) {
	global $CONFIG;

	$CONFIG->active_sticky_form = $form_name;
}
/**
 * Returns the PHP INI setting in bytes
 *
 * @param str $setting
 * @return int
 * @since 1.7
 * @link http://www.php.net/manual/en/function.ini-get.php
 */
function elgg_get_ini_setting_in_bytes($setting) {
	// retrieve INI setting
	$val = ini_get($setting);

	// convert INI setting when shorthand notation is used
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	// return byte value
	return $val;
}

/**
 * Server javascript pages.
 *
 * @param $page
 * @return unknown_type
 */
function js_page_handler($page) {
	if (is_array($page) && sizeof($page)) {
		$js = str_replace('.js','',$page[0]);
		$return = elgg_view('js/' . $js);

		header('Content-type: text/javascript');
		header('Expires: ' . date('r',time() + 864000));
		header("Pragma: public");
		header("Cache-Control: public");
		header("Content-Length: " . strlen($return));

		echo $return;
		exit;
	}
}

/**
 * This function is a shutdown hook registered on startup which does nothing more than trigger a
 * shutdown event when the script is shutting down, but before database connections have been dropped etc.
 *
 */
function __elgg_shutdown_hook() {
	global $START_MICROTIME;

	trigger_elgg_event('shutdown', 'system');

	$time = (float)(microtime(TRUE) - $START_MICROTIME);
	// demoted to NOTICE from DEBUG so javascript is not corrupted
	elgg_log("Page {$_SERVER['REQUEST_URI']} generated in $time seconds", 'NOTICE');
}

/**
 * Register functions for Elgg core
 *
 * @return unknown_type
 */
function elgg_init() {
	// Page handler for JS
	register_page_handler('js','js_page_handler');

	// Register an event triggered at system shutdown
	register_shutdown_function('__elgg_shutdown_hook');
}

function elgg_walled_garden_index() {
	$login = elgg_view('account/forms/login_walled_garden');
	echo elgg_view('page_shells/walled_garden', array(
		'body' => $login,
		'sysmessages' => system_messages(NULL, ''),
	));

	// @hack Index must exit to keep plugins from continuing to extend
	exit;
	return TRUE;
}

/**
 * Boot Elgg
 * @return unknown_type
 */
function elgg_boot() {
	// Actions
	register_action('comments/add');
	register_action('comments/delete');
	register_action('likes/add');
	register_action('likes/delete');

	elgg_view_register_simplecache('css');
	elgg_view_register_simplecache('js/friendsPickerv1');
	elgg_view_register_simplecache('js/initialise_elgg');
}

/**
 * Runs unit tests for the API.
 */
function elgg_api_test($hook, $type, $value, $params) {
	global $CONFIG;
	$value[] = $CONFIG->path . 'engine/tests/api/entity_getter_functions.php';
	$value[] = $CONFIG->path . 'engine/tests/api/helpers.php';
	$value[] = $CONFIG->path . 'engine/tests/regression/trac_bugs.php';
	return $value;
}

/**
 * Sorts out the featured URLs and the "more" dropdown
 * @return array ('featured_urls' and 'more')
 * @since 1.8
 */
function elgg_get_nav_items() {
	$menu_items = get_register('menu');
	$featured_urls_info = get_config('menu_items_featured_urls');

	$more = array();
	$featured_urls = array();
	$featured_urls_sanitised = array();

	// easier to compare with in_array() than embedded foreach()es
	$valid_urls = array();
	foreach ($menu_items as $info) {
		$valid_urls[] = $info->value->url;
	}

	// make sure the url is a valid link.
	// this prevents disabled plugins leaving behind
	// valid links when no using a pagehandler.
	foreach ($featured_urls_info as $info) {
		if (in_array($info->value->url, $valid_urls)) {
			$featured_urls[] = $info->value->url;
			$featured_urls_sanitised[] = $info;
		}
	}

	// add toolbar entries if not hiding dupes.
	foreach ($menu_items as $name => $info) {
		if (!in_array($info->value->url, $featured_urls)) {
			$more[] = $info;
		}
	}

	return array(
		'featured' => $featured_urls_sanitised,
		'more' => $more
	);
}

/**
 * Hook that registers the custom menu items.
 * @since 1.8
 */
function add_custom_menu_items() {
	if ($custom_items = get_config('menu_items_custom_items')) {
		foreach ($custom_items as $url => $name) {
			add_menu($name, $url);
		}
	}
}

/**
 * Test two URLs to see if they are functionally identical.
 *
 * @param string $url1
 * @param string $url2
 * @param array $ignore_params - GET params to ignore in the comparison
 * @return BOOL
 * @since 1.8
 */
function elgg_http_url_is_identical($url1, $url2, $ignore_params = array('offset', 'limit')) {
	global $CONFIG;

	// if the server portion is missing but it starts with / then add the url in.
	if (elgg_substr($url1, 0, 1) == '/') {
		$url1 = $CONFIG->url . ltrim($url1, '/');
	}

	if (elgg_substr($url1, 0, 1) == '/') {
		$url2 = $CONFIG->url . ltrim($url2, '/');
	}

	// @todo - should probably do something with relative URLs

	if ($url1 == $url2) {
		return TRUE;
	}

	$url1_info = parse_url($url1);
	$url2_info = parse_url($url2);

	$url1_info['path'] = trim($url1_info['path'], '/');
	$url2_info['path'] = trim($url2_info['path'], '/');

	// compare basic bits
	$parts = array('scheme', 'host', 'path');

	foreach ($parts as $part) {
		if ((isset($url1_info[$part]) && isset($url2_info[$part])) && $url1_info[$part] != $url2_info[$part]) {
			return FALSE;
		} elseif (isset($url1_info[$part]) && !isset($url2_info[$part])) {
			return FALSE;
		} elseif (!isset($url1_info[$part]) && isset($url2_info[$part])) {
			return FALSE;
		}
	}

	// quick compare of get params
	if (isset($url1_info['query']) && isset($url2_info['query']) && $url1_info['query'] == $url2_info['query']) {
		return TRUE;
	}

	// compare get params that might be out of order
	$url1_params = array();
	$url2_params = array();

	if (isset($url1_info['query'])) {
		if ($url1_info['query'] = html_entity_decode($url1_info['query'])) {
			$url1_params = elgg_parse_str($url1_info['query']);
		}
	}

	if (isset($url2_info['query'])) {
		if ($url2_info['query'] = html_entity_decode($url2_info['query'])) {
			$url2_params = elgg_parse_str($url2_info['query']);
		}
	}

	// drop ignored params
	foreach ($ignore_params as $param) {
		if (isset($url1_params[$param])) {
			unset($url1_params[$param]);
		}
		if (isset($url2_params[$param])) {
			unset($url2_params[$param]);
		}
	}

	// array_diff_assoc only returns the items in arr1 that aren't in arrN
	// but not the items that ARE in arrN but NOT in arr1
	// if arr1 is an empty array, this function will return 0 no matter what.
	// since we only care if they're different and not how different,
	// add the results together to get a non-zero (ie, different) result
	$diff_count = count(array_diff_assoc($url1_params, $url2_params));
	$diff_count += count(array_diff_assoc($url2_params, $url1_params));
	if ($diff_count > 0) {
		return FALSE;
	}

	return TRUE;
}

function elgg_walled_garden() {
	global $CONFIG;

	// check for external page view
	if (isset($CONFIG->site) && $CONFIG->site instanceof ElggSite) {
		$CONFIG->site->check_walled_garden();
	}
}

/**
 * Some useful constant definitions
 */
define('ACCESS_DEFAULT', -1);
define('ACCESS_PRIVATE', 0);
define('ACCESS_LOGGED_IN', 1);
define('ACCESS_PUBLIC', 2);
define('ACCESS_FRIENDS', -2);

/**
 * @since 1.7
 */
define('ELGG_ENTITIES_ANY_VALUE', NULL);
define('ELGG_ENTITIES_NO_VALUE', 0);

/**
 * @since 1.7.2
 */
define('REFERRER', -1);
define('REFERER', -1);

register_elgg_event_handler('init', 'system', 'elgg_init');
register_elgg_event_handler('boot', 'system', 'elgg_boot', 1000);
register_plugin_hook('unit_test', 'system', 'elgg_api_test');

register_elgg_event_handler('init', 'system', 'add_custom_menu_items', 1000);
register_elgg_event_handler('init', 'system', 'elgg_walled_garden', 1000);
