<?php
namespace Minds\Helpers;

use Minds\Core;
use Minds\Core\Events;

/**
 * Subscriptions helpers
 * 
 * Helper methods for subscriptions
 */
class Subscriptions{

    /**
     * Subscribe a user to a user
     * @param long $user_guid - the user who is doing the action, eg. me
     * @param long $to_guid - the user to subscribe to
     * @return boolean
     */
    public static function subscribe($user_guid, $to_guid, $data = array()){
        $return = false;
        if(empty($data))
            $data = time();
        
        $friends = new Core\Data\Call('friends');
        $friendsof = new Core\Data\Call('friendsof');
        
        
        if(is_array($data))
            $data = json_encode($data);
        
        if($friends->insert($user_guid, array($to_guid=>$data)) && $friendsof->insert($to_guid, array($user_guid=>$data)))
            $return =  true;
        
        $prepared = new Core\Data\Neo4j\Prepared\Common();
        $return =  Core\Data\Client::build('Neo4j')->request($prepared->createSubscription($user_guid, $to_guid));

        //grab the newsfeed
        $nf = new Core\Data\Call('entities_by_time');
        $feed = $nf->getRow("activity:user:own:$to_guid", array('limit'=>12));
        if($feed)
            $nf->insert("activity:network:$user_guid", $feed);

        \Minds\Core\Data\cache\factory::build()->set("$user_guid:friendof:$to_guid", 'yes');
        Events\Dispatcher::trigger('subscribe', 'all', array('user_guid'=>$user_guid, 'to_guid'=>$to_guid));        
        return $return;
    }
    
    public static function unSubscribe($user, $from){
        
    }
    
    public static function isSubscribed($user, $to){
    }
    
    public static function getSubscriptions($user){
    }
    
    public static function getSubscribers($user){
    }
        
}   
