<?php

namespace Minds\Core\Boost;

use Minds\Core;
use Minds\Core\Data;
use minds\interfaces;
use minds\entities;

/**
 * Channel boost handler
 */
class Channel implements interfaces\BoostHandlerInterface{
    
    private $guid;
   
    public function __construct($options){
       $this->guid = $options['direction'];
    }
    
   /**
     * Boost an entity
     * @param object/int $entity - the entity to boost
     * @param int $points
     * @return boolean
     */
    public function boost($entity, $points){
            
        if(is_object($entity)){
            $guid = $entity->guid;
        } else {
            $guid = $entity;
        }
        
        $db = new Data\Call('entities_by_time');
        $result = $db->insert("boost:channel:$this->guid:review", array($guid => $points));
        
        //send a notification of boost offer
        Core\Events\Dispatcher::trigger('notification', 'elgg/hook/activity', array(
                'to' => array($this->guid),
                'object_guid' => $guid,
                'notification_view' => 'boost_request',
                'params' => array('points'=>$_POST['points']),
                'points' => $points
                ));
        
        return $result;
    }
    
     /**
     * Return boosts for review
     * @param int $limit
     * @param string $offset
     * @return array
     */
    public function getReviewQueue($limit, $offset = ""){
        $db = new Data\Call('entities_by_time');
        $guids = $db->getRow("boost:channel:$this->guid:review", array('limit'=>$limit, 'offset'=>$offset));
        return $guids;
    }
    
    /**
     * Accept a boost and do a remind
     * @param object/int $entity
     * @param int points
     * @return boolean
     */
    public function accept($entity, $points){

        if(is_object($entity)){
            $guid = $entity->guid;
        } else {
            $guid = $entity;
        }
        
        $db = new Data\Call('entities_by_time');
        
        
        $embeded = new entities\entity($guid);
        $embeded = core\entities::build($embeded); //more accurate, as entity doesn't do this @todo maybe it should in the future
        \Minds\Helpers\Counters::increment($pages[1], 'remind');

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

        //remove from review
        $db->removeAttributes("boost:channel:$this->guid:review", array($guid));
        
        $entity = new \Minds\entities\activity($guid);
        Core\Events\Dispatcher::trigger('notification', 'elgg/hook/activity', array(
            'to'=>array($entity->owner_guid),
            'object_guid' => $guid,
            'title' => $entity->title,
            'notification_view' => 'boost_accepted',
            'params' => array('points'=>$points),
            'points' => $points
            ));
        return true;
    }

    /**
     * Reject a boost
     * @param object/int $entity
     * @return boolean
     */
    public function reject($entity){
        
        ///
        /// REFUND THE POINTS TO THE USER
        ///
        
        
        if(is_object($entity)){
            $guid = $entity->guid;
        } else {
            $guid = $entity;
        }
        $db = new Data\Call('entities_by_time');
        $db->removeAttributes("boost:channel:$this->guid:review", array($guid));

        $entity = new \Minds\entities\activity($guid);
        Core\Events\Dispatcher::trigger('notification', 'elgg/hook/activity', array(
            'to'=>array($entity->owner_guid),
            'object_guid' => $guid,
            'title' => $entity->title,
            'notification_view' => 'boost_rejected',
            ));
        return true;//need to double check somehow..
    }
    
    /**
     * Return a boost
     * @return array
     */
    public function getBoost($offset = ""){
       
       ///
       //// THIS DOES NOT APPLY BECAUSE IT'S PRE-AGREED
       ///
       
    }
        
}
