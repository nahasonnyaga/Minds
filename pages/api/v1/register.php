<?php
/**
 * Minds Subscriptions
 * 
 * @version 1
 * @author Mark Harding
 */
namespace minds\pages\api\v1;

use Minds\Core;
use minds\entities;
use minds\interfaces;
use Minds\Api\Factory;

class register implements interfaces\api, interfaces\ApiIgnorePam{

    /**
     * NOT AVAILABLE
     */      
    public function get($pages){
                
        return Factory::response(array('status'=>'error', 'message'=>'GET is not supported for this endpoint'));
        
    }
    
    /**
     * Registers a user
     * @param array $pages
     * 
     * API:: /v1/register
     */
    public function post($pages){
       
        try{
            $guid = register_user($_POST['username'], $_POST['password'], $_POST['username'], $_POST['email'], false);
            $params = array(
                'user' => new entities\user($guid),
                'password' => $_POST['password'],
                'friend_guid' => "",
                'invitecode' => ""
            );
            elgg_trigger_plugin_hook('register', 'user', $params, TRUE);
            
            \Minds\plugin\payments\start::createTransaction($guid, 100, $guid, "Welcome.");
            Core\Events\Dispatcher::trigger('notification', 'elgg/hook/activity', array(
                'to'=>array($guid),
                'from' => 100000000000000519,
                'notification_view' => 'welcome_points',
                'params' => array('points'=>100),
                'points' => 100
                ));

            //@todo maybe put this in background process
            foreach(array("welcome_boost", "welcome_chat", "welcome_discover") as $notif_type){
               Core\Events\Dispatcher::trigger('notification', 'elgg/hook/activity', array(
                'to'=>array($guid),
                'from' => "100000000000000519",
                'notification_view' => $notif_type,
                )); 
            }


            $response = array('guid'=>$guid);
        } catch (\Exception $e){
            $response = array('status'=>'error', 'message'=>$e->getMessage());
        }
        return Factory::response($response);
        
    }
    
    public function put($pages){}
    
    public function delete($pages){}
    
}
        
