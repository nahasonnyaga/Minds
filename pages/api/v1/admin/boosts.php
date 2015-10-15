<?php
/**
 * Minds Admin: Boosts
 *
 * @version 1
 * @author Mark Harding
 *
 */
namespace minds\pages\api\v1\admin;

use Minds\Core;
use Minds\Helpers;
use Minds\Entities;
use Minds\Interfaces;
use Minds\Api\Factory;

class boosts implements Interfaces\Api{

    private $rate = 1;

    /**
     * Returns a list of boosts
     */
    public function get($pages){
      $response = array();

      $limit = isset($_GET['limit']) ? $_GET['limit'] : 12;
      $offset = isset($_GET['offset']) ? $_GET['offset'] : "";
      $type = isset($_GET['type']) ? $_GET['type'] : 'Newsfeed';
      $queue = Core\Boost\Factory::build(ucfirst($type))->getReviewQueue($limit, $offset);
      $count =  Core\Boost\Factory::build(ucfirst($type))->getReviewQueueCount();

      $guids = array();
      foreach($queue as $data){
          $_id = (string) $data['_id'];
          $guids[$_id] = $data['guid'];
      }

      if($guids){
        $entities = Core\Entities::get(array('guids' => $guids));
        $db = new Core\Data\Call('entities_by_time');
        foreach($entities as $k => $entity){
          foreach($queue as $data){
              if($data['guid'] == $entity->guid){
                  $entities[$k]->boost_impressions = $data['impressions'];
                  $entities[$k]->boost_id = (string) $data['_id'];
              }
          }
        }
        $response['entities'] = Factory::exportable($entities, array('boost_impressions', 'boost_id'));
        $response['count'] = $count;
        $response['load-next'] = $_id;
    }


        return Factory::response($response);
    }

    /**
     * Approve a boost
     * @param array $pages
     */
    public function post($pages){

      $response = array();

      $type = isset($_POST['type']) ? $_POST['type'] : 'Newsfeed';
      if($_POST['action'] == 'accept'){
        Core\Boost\Factory::build(ucfirst($type))->accept($_POST['_id']);
		  } elseif($_POST['action'] == 'reject') {

        Core\Boost\Factory::build(ucfirst($type))->reject($_POST['_id']);
        $entity = \Minds\entities\Factory::build($_POST['guid']);
        if($entity->type == "user"){
            $user_guid = $entity->guid;
        } else {
            $user_guid = $entity->owner_guid;
        }
        //refund the point
        \Minds\plugin\payments\start::createTransaction($user_guid, $_POST['impressions'] / $this->rate, NULL, "boost refund");
      }

      return Factory::response($response);
    }

    /**
     * @param array $pages
     */
    public function put($pages){
	    return Factory::response(array());
    }

    /**
     * @param array $pages
     */
    public function delete($pages){
        return Factory::response(array());
    }

}
