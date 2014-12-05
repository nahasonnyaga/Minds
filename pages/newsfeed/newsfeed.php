<?php
/**
 * Minds newsfeed feed page
 */
namespace minds\pages\newsfeed;

use minds\core;
use minds\entities;
use minds\interfaces;

class newsfeed extends core\page implements interfaces\page{
	
	public $context = 'newsfeed';
	
	/**
	 * Setup, only applies to the newsfeed page
	 */
	public function pageSetup($hook, $type, $return, $params) {

		if($params['entity']->type != 'activity'){
			return $return;
		}
		
		$activity = $params['entity'];
		
		foreach($return as $id => $item){
			if(in_array($item->getName(), array('edit', 'access', 'delete')))
				unset($return[$id]);
		}
	
		$options = array(
				'name' => 'remind',
				'href' => "newsfeed/remind/$activity->guid",
				'text' => '<span class="entypo">&#59159;</span> Remind',
				'class' => '',
				'title' => elgg_echo('minds:remind'),
				'is_action' => true,
				'priority' => 1,
			);
		$return[] = \ElggMenuItem::factory($options);

		
		elgg_load_js('lightbox');
		elgg_load_css('lightbox');
		$options = array(
					'name' => 'embed',
					'href' => "newsfeed/$activity->guid/embed",
					'text' => '<span class="entypo">&#59406;</span> Embed',
					'class' => 'elgg-lightbox',
					'title' => elgg_echo('minds:embed'),
					'is_action' => true,
					'priority' => 1,
				);
		$return[] = \ElggMenuItem::factory($options);
		
		
		$options = array(
					'name' => 'share',
					'href' => "newsfeed/$activity->guid/share",
					'text' => '<span class="entypo">&#59407;</span> Link',
					'class' => 'elgg-lightbox',
					'title' => elgg_echo('minds:share'),
					'is_action' => true,
					'priority' => 1,
				);
		$return[] = \ElggMenuItem::factory($options);
		
		if($activity->canEdit()){
			$options = array(
					'name' => 'delete',
					'href' => "newsfeed/$activity->guid/delete",
					'text' => '<span class="entypo">&#10062;</span> Delete',
					'class' => 'ajax-non-action',
					'title' => elgg_echo('delete'),
					'is_action' => false,
					'priority' => 999,
				);
			$return[] = \ElggMenuItem::factory($options);
		}
		
		return $return;
	}
	
	/**
	 * Get
	 */
	public function get($pages){
			
		if(!\minds\core\session::isLoggedin() && !isset($pages[0]))
			$this->forward('login');
		
		\elgg_register_plugin_hook_handler('register', 'menu:entity', array($this, 'pageSetup'));
		
		if(!isset($pages[0])){
			$pages[0] = 'network';
		}
		
		//if(!elgg_is_logged_in()){
		//	$this->forward('login');
		//}
		
		if(!is_numeric($pages[0]) && \minds\core\session::isLoggedin() && elgg_get_logged_in_user_entity()->getSubscriptionsCount() == 0 && !elgg_get_logged_in_user_entity()->base_node){
			$pages[0] = 'featured';
		}

		switch($pages[0]){
			case is_numeric($pages[0]):
				switch($pages[1]){
					case 'embed':
						
						$code = '<div class="minds-post" data-guid="'.$pages[0].'"></div><script async src="'.elgg_get_site_url().'js/widgets.0.js"></script>';
						echo '<p>Copy this post to your website by copying the code below</p>';
						echo elgg_view('input/text', array('value'=>$code, 'style'=>'width:640px'));
						
						echo "<h3>Preview</h3>";
						echo $code;
						echo '<script>
								$("input[type=\'text\']").select();
								$("input[type=\'text\']").on("click", function () {
		  							 $(this).select();
								});
							</script>';
						return true;
						break;
					case 'share':
							$url = elgg_get_site_url() . 'newsfeed/'.$pages[0];
							echo '<p>Copy the url below to share this post</p>';
							echo elgg_view('input/text', array('value'=>$url, 'style'=>'width:400px'));
							
							echo '<script>
									$("input[type=\'text\']").select();
									$("input[type=\'text\']").on("click", function () {
			  							 $(this).select();
									});
								</script>';
							
							return true;
						break;
					case 'delete':
						$activity = new entities\activity($pages[0]);
						if($activity->delete()){
							system_message('Success!');
						} else {
							register_error('Ooops! Try again');
						}
			
						$this->forward(REFERRER);
						break;
					default: 
						$options = array(
							'guids' => array($pages[0]),
							'limit' => 1,
							'pagination' => false,
							'prepend' => ''
						);
				}
				break;
			case 'mine':
			case 'user':
				$options = array(
					'owner_guid' => isset($pages[1]) ? $pages[1] : elgg_get_logged_in_user_guid()
				);
				break;
			case 'all':
				if(!elgg_is_admin_logged_in()){
					return false;
				}
				$options = array(

				);
				break;
			case 'featured':
				$options = array(
					'attrs' => array('namespace'=>'activity:featured')
				);
				break;
			case 'network':
			default:
				$options = array(
					'network' => isset($pages[1]) ? $pages[1] : elgg_get_logged_in_user_guid()
				);
		}
		
		$post = elgg_view_form('activity/post', array('action'=>'newsfeed/post', 'enctype'=>'multipart/form-data'));
		
		$content .= core\entities::view(array_merge(array(
			'type' => 'activity',
			'limit' => get_input('limit', 5),
			'masonry' => false,
			'prepend' => $post,
			'list_class' => 'list-newsfeed'
		), $options));
		
		$sidebar_left = elgg_view('channel/sidebar', array(
			'user' => elgg_get_logged_in_user_entity()
		));
		
		$sidebar_right = "<b style='margin-top:12px;display:block;'>Filter</b>";
		\elgg_register_menu_item('page', array('text'=>'Network', 'href'=>'newsfeed/network', 'name'=>'network', 'selected' =>isset($options['network'])));
		\elgg_register_menu_item('page', array('text'=>'Personal', 'href'=>'newsfeed/mine', 'name'=>'mine', 'selected'=> isset($options['owner_guid'])));
		\elgg_register_menu_item('page', array('text'=>'Featured', 'href'=>'newsfeed/featured', 'name'=>'featured', 'selected'=> isset($options['attrs'])));
		if(elgg_is_admin_logged_in())
			\elgg_register_menu_item('page', array('text'=>'All (admins only)', 'href'=>'newsfeed/all', 'name'=>'all', !isset($options['network']) || !isset($options['owner_guid'])));
		
		$body = \elgg_view_layout('two_sidebar', array(
			'title'=>\elgg_echo('newsfeed'), 
			'content'=>$content, 
			'class' => 'newsfeed',
			'sidebar_top'=>$sidebar_right,
			//'sidebar' => elgg_view_menu('footer'),
			'sidebar_alt'=>$sidebar_left,
			'sidebar-alt-class' =>  'minds-fixed-sidebar-left'
		));
		
		echo $this->render(array('body'=>$body));
	}
	
	/**
	 * POST
	 */
	public function post($pages){

		switch($pages[0]){
			case 'post':
				$activity = new entities\activity();
				if(isset($_POST['message']))
					$activity->setMessage($_POST['message']);
				
				/**
				 * Is there a rich embed?
				 */
				if(isset($_POST['title'])){
					$activity->setTitle($_POST['title'])
						->setBlurb($_POST['description'])
						->setURL(\elgg_normalize_url($_POST['url']))
						->setThumbnail($_POST['thumbnail']);
				}
				
				/**
				 * Is there an attachment
				 */
				if(isset($_FILES['attachment']) && $_FILES['attachment']['tmp_name']){
					
					$attachment = new \PostAttachment();
					$guid = $attachment->save($_FILES['attachment']);
					
					
							
					if(in_array($_FILES['attachment']['type'], array('image/jpeg', 'image/png', 'image/gif', 'image/bmp'))){
						$activity->setCustom('batch', array(array(
							'src' => $attachment->getIconURL('medium'),
							'href' => elgg_get_site_url() . 'archive/view/' . $attachment->container_guid . '/' . $attachment->guid
						)));
					} else {
						$activity->setTitle($_FILES['attachment']['name'])
							->setURL($attachment->getURL());
					}
					
					
							
				}
				
				if(isset($_POST['container_guid']) && $container_guid = $_POST['container_guid']){
					$activity->container_guid = $container_guid ;
					$activity->access_id = $container_guid ;
					$activity->indexes = array("activity:container:$container_guid");
				}
			
				if(isset($_POST['to_guid']) && $_POST['to_guid'] != elgg_get_logged_in_user_guid()){
					 $activity->indexes = array("activity:user:".$_POST['to_guid']);	
				}
	
				$activity->save();
				$this->forward(REFERRER);
				exit;
			case 'remind':
				//a remind is not a post, it is repost
				$embeded = new entities\entity($pages[1]);
				$embeded = core\entities::build($embeded); //more accurate, as entity doesn't do this @todo maybe it should in the future
				$activity = new entities\activity();
				switch($embeded->type){
					case 'activity':
						if($embeded->remind_object)
							$activity->setRemind($embeded->remind_object)->save();
						else
							$activity->setRemind($embeded->export())->save();
						break;
					default:
						/**
						 * The following are actually treated as embeded posts. 
						 */
						switch($embeded->subtype){
							case 'blog':
								$message = false;
								if($embeded->owner_guid != elgg_get_logged_in_user_guid())
									$message = 'via <a href="'.$embeded->getOwnerEntity()->getURL() . '">'. $embeded->getOwnerEntity()->name . '</a>';
								$activity->setTitle($embeded->title)
									->setBlurb(elgg_get_excerpt($embeded->description))
									->setURL($embeded->getURL())
									->setThumbnail($embeded->getIconUrl())
									->setMessage($message)
									->setFromEntity($embeded)
									->save();
							break;
						}
						
				}
			case 'api':
				error_log('Answering api activity request');	
				if(!isset($pages[1])){
					error_log('feed guid not supplied');
					echo json_encode(array('error'=>'You must supply the feed guid in the request uri'));
					return false;
				}
				$subscriber_guid = $pages[1];
				
				$ia = elgg_set_ignore_access(true);
					
				/**
				 * First off, lets just verify our user exists, and is in fact subscribed to this user
				 */
				$db = new core\data\call('friends');
				$subscription = $db->getRow($subscriber_guid, array('limit'=> 1, 'offset'=>$_POST['owner_guid']));
				
				if(key($subscription) != $_POST['owner_guid']){
					error_log('we are not a subscriber');
					echo json_encode(array('error'=> "$subscriber_guid is not a subscriber."));
					return true;
				}
				
				$payload = json_decode(reset($subscription), true);
				$secret = $payload['secret'];//the shared secret
				
				/**
				 * @todo check the origin is correct
				 */
				
				/**
				 * Validate our signature..
				 */
				$signature = \minds\core\clusters::generateSignature($_POST, $secret);
				if($_SERVER['HTTP_X_MINDS_SIGNATURE'] != $signature){
					error_log('wrong signature');
					echo json_encode(array('error'=>'Incorrect signature. Please check the secret key'));
					return false;
				}

				error_log(print_r($_POST['ownerObj'], true));
				$activity = new \minds\entities\activity($_POST);
				$activity->external = true;
				$new->indexes = array(
					'activity:network:'. $subscriber_guid 
				);
				$activity->save();


				elgg_set_ignore_access($ia); //cancel access
			break;
		}
	}
	
	public function put($pages){
		throw new \Exception('Sorry, the put method is not supported for the page');
	}
	
	public function delete($pages){
		throw new \Exception('Sorry, the delete method is not supported for the page');
	}
	
}
