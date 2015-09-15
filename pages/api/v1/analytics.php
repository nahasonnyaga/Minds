<?php
/**
 * Minds Analytics Api endpoint
 *
 * @version 1
 * @author Mark Harding
 *
 */
namespace minds\pages\api\v1;
use Swagger\Annotations as SWG;

use Minds\Core;
use Minds\Helpers;
use minds\entities;
use minds\interfaces;
use Minds\Api\Factory;

class analytics implements interfaces\api{

    public function get($pages){
    }

    public function post($pages){
    }

    /**
     * Sets an analytic
     * @param array $pages
     * @SWG\PUT(
     *     tags={"analytics"},
     *     summary="Send an analytic metric",
     *     path="/v1/analytics",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(name="200", description="An example resource", @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Pet")
     *         )),
     *  security={
     *         {
     *             "minds_oauth2": {}
     *         }
     *     }
     * )
     */
    public function put($pages){
        switch($pages[0]){
            case 'open':
                Helpers\Analytics::increment("app-opens");
                //CAMPAIGN:: Reward 10 points per day if a user opens their app
                $db = new Core\Data\Call('entities_by_time');
                $ts = Helpers\Analytics::buildTS("day", time());
                $row = $db->getRow("analytics:rewarded:day:$ts", array('offset'=>Core\session::getLoggedinUser()->guid, 'limit'=>1));
                if(!$row || key($row) != Core\session::getLoggedinUser()->guid){
                  $db->insert("analytics:rewarded:day:$ts", array(Core\session::getLoggedinUser()->guid => time()));

                  \Minds\plugin\payments\start::createTransaction(Core\session::getLoggedinUser()->guid, 10, Core\session::getLoggedinUser()->guid, "Daily login reward.");
                  Core\Events\Dispatcher::trigger('notification', 'elgg/hook/activity', array(
                      'to'=>array(Core\session::getLoggedinUser()->guid),
                      'from' => 100000000000000519,
                      'notification_view' => 'custom_message',
                      'params' => array('message'=>"We gave you 10 points for logging in!"),
                      'message'=>"We gave you 10 points for logging in!"
                      ));
                }
            break;
        }

        return Factory::response(array());
    }

    public function delete($pages){
    }

}
