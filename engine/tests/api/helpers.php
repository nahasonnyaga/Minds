<?php
/**
 * Elgg Test helper functions
 *
 *
 * @package Elgg
 * @subpackage Test
 */
class ElggCoreHelpersTest extends ElggCoreUnitTest {

	/**
	 * Called before each test object.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Called before each test method.
	 */
	public function setUp() {

	}

	/**
	 * Called after each test method.
	 */
	public function tearDown() {
		// do not allow SimpleTest to interpret Elgg notices as exceptions
		$this->swallowErrors();

		global $CONFIG;
		unset($CONFIG->externals);
	}

	/**
	 * Called after each test object.
	 */
	public function __destruct() {
		// all __destruct() code should go above here
		parent::__destruct();
	}

	/**
	 * Test elgg_instanceof()
	 */
	public function testElggInstanceOf() {
		$entity = new ElggObject();
		$entity->subtype = 'test_subtype';
		$entity->save();

		$this->assertTrue(elgg_instanceof($entity));
		$this->assertTrue(elgg_instanceof($entity, 'object'));
		$this->assertTrue(elgg_instanceof($entity, 'object', 'test_subtype'));

		$this->assertFalse(elgg_instanceof($entity, 'object', 'invalid_subtype'));
		$this->assertFalse(elgg_instanceof($entity, 'user', 'test_subtype'));

		$entity->delete();

		$bad_entity = FALSE;
		$this->assertFalse(elgg_instanceof($bad_entity));
		$this->assertFalse(elgg_instanceof($bad_entity, 'object'));
		$this->assertFalse(elgg_instanceof($bad_entity, 'object', 'test_subtype'));
	}

	/**
	 * Test elgg_normalize_url()
	 */
	public function testElggNormalizeURL() {
		$conversions = array(
			'http://example.com' => 'http://example.com',
			'https://example.com' => 'https://example.com',
			'//example.com' => '//example.com',

			'example.com' => 'http://example.com',
			'example.com/subpage' => 'http://example.com/subpage',

			'page/handler' =>                	elgg_get_site_url() . 'page/handler',
			'page/handler?p=v&p2=v2' =>      	elgg_get_site_url() . 'page/handler?p=v&p2=v2',
			'mod/plugin/file.php' =>            elgg_get_site_url() . 'mod/plugin/file.php',
			'mod/plugin/file.php?p=v&p2=v2' =>  elgg_get_site_url() . 'mod/plugin/file.php?p=v&p2=v2',
			'rootfile.php' =>                   elgg_get_site_url() . 'rootfile.php',
			'rootfile.php?p=v&p2=v2' =>         elgg_get_site_url() . 'rootfile.php?p=v&p2=v2',

			'/page/handler' =>               	elgg_get_site_url() . 'page/handler',
			'/page/handler?p=v&p2=v2' =>     	elgg_get_site_url() . 'page/handler?p=v&p2=v2',
			'/mod/plugin/file.php' =>           elgg_get_site_url() . 'mod/plugin/file.php',
			'/mod/plugin/file.php?p=v&p2=v2' => elgg_get_site_url() . 'mod/plugin/file.php?p=v&p2=v2',
			'/rootfile.php' =>                  elgg_get_site_url() . 'rootfile.php',
			'/rootfile.php?p=v&p2=v2' =>        elgg_get_site_url() . 'rootfile.php?p=v&p2=v2',
		);

		foreach ($conversions as $input => $output) {
			$this->assertIdentical($output, elgg_normalize_url($input));
		}
	}


	/**
	 * Test elgg_register_js()
	 */
	public function testElggRegisterJS() {
		global $CONFIG;

		// specify name
		$result = elgg_register_js('key', 'http://test1.com', 'footer');
		$this->assertTrue($result);
		$this->assertIdentical('http://test1.com', $CONFIG->externals['js']['key']->url);

		// send a bad url
		$result = @elgg_register_js('bad');
		$this->assertFalse($result);
	}

	/**
	 * Test elgg_register_css()
	 */
	public function testElggRegisterCSS() {
		global $CONFIG;

		// specify name
		$result = elgg_register_css('key', 'http://test1.com');
		$this->assertTrue($result);
		$this->assertIdentical('http://test1.com', $CONFIG->externals['css']['key']->url);
	}

	/**
	 * Test elgg_unregister_js()
	 */
	public function testElggUnregisterJS() {
		global $CONFIG;

		$base = trim(elgg_get_site_url(), "/");

		$urls = array('id1' => "$base/urla", 'id2' => "$base/urlb", 'id3' => "$base/urlc");
		foreach ($urls as $id => $url) {
			elgg_register_js($id, $url);
		}

		$result = elgg_unregister_js('id1');
		$this->assertTrue($result);
		@$this->assertNULL($CONFIG->externals['js']['head']['id1']);

		$result = elgg_unregister_js('id1');
		$this->assertFalse($result);
		$result = elgg_unregister_js('', 'does_not_exist');
		$this->assertFalse($result);

		$result = elgg_unregister_js('id2');
		$this->assertIdentical($urls['id3'], $CONFIG->externals['js']['id3']->url);
	}

	/**
	 * Test elgg_load_js()
	 */
	public function testElggLoadJS() {
		global $CONFIG;

		// load before register
		elgg_load_js('key');
		$result = elgg_register_js('key', 'http://test1.com', 'footer');
		$this->assertTrue($result);
		$js_urls = elgg_get_loaded_js('footer');
		$this->assertIdentical(array('http://test1.com'), $js_urls);
	}

	/**
	 * Test elgg_get_loaded_js()
	 */
	public function testElggGetJS() {
		global $CONFIG;

		$base = trim(elgg_get_site_url(), "/");

		$urls = array('id1' => "$base/urla", 'id2' => "$base/urlb", 'id3' => "$base/urlc");
		foreach ($urls as $id => $url) {
			elgg_register_js($id, $url);
			elgg_load_js($id);
		}

		$js_urls = elgg_get_loaded_js('head');
		$this->assertIdentical($js_urls[0], $urls['id1']);
		$this->assertIdentical($js_urls[1], $urls['id2']);
		$this->assertIdentical($js_urls[2], $urls['id3']);

		$js_urls = elgg_get_loaded_js('footer');
		$this->assertIdentical(array(), $js_urls);
	}

	// test ElggPriorityList
	public function testElggPriorityListAdd() {
		$pl = new ElggPriorityList();
		$elements = array(
			'Test value',
			'Test value 2',
			'Test value 3'
		);

		shuffle($elements);

		foreach ($elements as $element) {
			$this->assertTrue($pl->add($element) !== false);
		}

		$test_elements = $pl->getElements();

		$this->assertTrue(is_array($test_elements));

		foreach ($test_elements as $i => $element) {
			// should be in the array
			$this->assertTrue(in_array($element, $elements));

			// should be the only element, so priority 0
			$this->assertEqual($i, array_search($element, $elements));
		}
	}

	public function testElggPriorityListAddWithPriority() {
		$pl = new ElggPriorityList();

		$elements = array(
			10 => 'Test Element 10',
			5 => 'Test Element 5',
			0 => 'Test Element 0',
			100 => 'Test Element 100',
			-1 => 'Test Element -1',
			-5 => 'Test Element -5'
		);

		foreach ($elements as $priority => $element) {
			$pl->add($element, $priority);
		}

		$test_elements = $pl->getElements();

		// should be sorted by priority
		$elements_sorted = array(
			-5 => 'Test Element -5',
			-1 => 'Test Element -1',
			0 => 'Test Element 0',
			5 => 'Test Element 5',
			10 => 'Test Element 10',
			100 => 'Test Element 100',
		);

		$this->assertIdentical($elements_sorted, $test_elements);

		foreach ($test_elements as $priority => $element) {
			$this->assertIdentical($elements[$priority], $element);
		}
	}

	public function testElggPriorityListGetNextPriority() {
		$pl = new ElggPriorityList();

		$elements = array(
			2 => 'Test Element',
			0 => 'Test Element 2',
			-2 => 'Test Element 3',
		);

		foreach ($elements as $priority => $element) {
			$pl->add($element, $priority);
		}

		// we're not specifying a priority so it should be the next consecutive to 0.
		$this->assertEqual(1, $pl->getNextPriority());

		// add another one at priority 1
		$pl->add('Test Element 1');

		// next consecutive to 0 is now 3.
		$this->assertEqual(3, $pl->getNextPriority());
	}

	public function testElggPriorityListRemove() {
		$pl = new ElggPriorityList();

		$elements = array();
		for ($i=0; $i<3; $i++) {
			$element = new stdClass();
			$element->name = "Test Element $i";
			$element->someAttribute = rand(0, 9999);
			$elements[] = $element;
			$pl->add($element);
		}

		$pl->remove($elements[1]);

		$test_elements = $pl->getElements();

		// make sure it's gone.
		$this->assertTrue(2, count($test_elements));
		$this->assertIdentical($elements[0], $test_elements[0]);
		$this->assertIdentical($elements[2], $test_elements[2]);
	}

	public function testElggPriorityListConstructor() {
		$elements = array(
			10 => 'Test Element 10',
			5 => 'Test Element 5',
			0 => 'Test Element 0',
			100 => 'Test Element 100',
			-1 => 'Test Element -1',
			-5 => 'Test Element -5'
		);

		$pl = new ElggPriorityList($elements);
		$test_elements = $pl->getElements();

		$elements_sorted = array(
			-5 => 'Test Element -5',
			-1 => 'Test Element -1',
			0 => 'Test Element 0',
			5 => 'Test Element 5',
			10 => 'Test Element 10',
			100 => 'Test Element 100',
		);

		$this->assertIdentical($elements_sorted, $test_elements);
	}

	public function testElggPriorityListGetPriority() {
		$pl = new ElggPriorityList();

		$elements = array(
			'Test element 0',
			'Test element 1',
			'Test element 2',
		);

		foreach ($elements as $element) {
			$pl->add($element);
		}

		$this->assertIdentical(0, $pl->getPriority($elements[0]));
		$this->assertIdentical(1, $pl->getPriority($elements[1]));
		$this->assertIdentical(2, $pl->getPriority($elements[2]));
	}

	public function testElggPriorityListPriorityCollision() {
		$pl = new ElggPriorityList();
		
		$elements = array(
			5 => 'Test element 5',
			6 => 'Test element 6',
			0 => 'Test element 0',
		);

		foreach ($elements as $priority => $element) {
			$pl->add($element, $priority);
		}

		// add at a colliding priority
		$pl->add('Colliding element', 5);

		// should float to the top closest to 5, so 7
		$this->assertEqual(7, $pl->getPriority('Colliding element'));
	}

	public function testElggPriorityListArrayAccess() {
		$pl = new ElggPriorityList();
		$pl[] = 'Test element 0';
		$pl[-10] = 'Test element -10';
		$pl[-1] = 'Test element -1';
		$pl[] = 'Test element 1';
		$pl[5] = 'Test element 5';
		$pl[0] = 'Test element collision with 0 (should be 2)';

		$elements = array(
			-1 => 'Test element -1',
			0 => 'Test element 0',
			1 => 'Test element 1',
			2 => 'Test element collision with 0 (should be 2)',
			5 => 'Test element 5',
		);

		$priority = $pl->getPriority('Test element -10');
		unset($pl[$priority]);

		$test_elements = $pl->getElements();
		$this->assertIdentical($elements, $test_elements);
	}

	public function testElggPriorityListIterator() {
		$elements = array(
			-5 => 'Test element -5',
			0 => 'Test element 0',
			5 => 'Test element 5'
		);
		
		$pl = new ElggPriorityList($elements);

		foreach ($pl as $priority => $element) {
			$this->assertIdentical($elements[$priority], $element);
		}
	}

	public function testElggPriorityListCountable() {
		$pl = new ElggPriorityList();

		$this->assertEqual(0, count($pl));

		$pl[] = 'Test element 0';
		$this->assertEqual(1, count($pl));

		$pl[] = 'Test element 1';
		$this->assertEqual(2, count($pl));

		$pl[] = 'Test element 2';
		$this->assertEqual(3, count($pl));
	}

	public function testElggPriorityListUserSort() {
		$elements = array(
			'A',
			'B',
			'C',
			'D',
			'E',
		);

		$elements_sorted_string = $elements;

		shuffle($elements);
		$pl = new ElggPriorityList($elements);

		// will sort by priority
		$test_elements = $pl->getElements();
		$this->assertIdentical($elements, $test_elements);

		function test_sort($elements) {
			sort($elements, SORT_LOCALE_STRING);
			return $elements;
		}

		// force a new sort using our function
		$pl->sort('test_sort');
		$test_elements = $pl->getElements();

		$this->assertIdentical($elements_sorted_string, $test_elements);
	}
}