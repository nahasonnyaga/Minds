<?php
/**
 * Minds messenger conversations
 */

namespace Minds\Plugin\Messenger\Core;

use Minds\Core\Di\Di;
use Minds\Core\Session;
use Minds\Entities\User;
use Minds\Plugin\Messenger;

class Conversations
{

    private $db;
    private $redis;
    private $user;

    public function __construct($db = NULL, $redis = NULL, $config = NULL)
    {
        $this->db = $db ?: Di::_()->get('Database\Cassandra\Indexes');
        $this->redis = $redis ?: new \Redis();
        $this->config = $config ?: Di::_()->get('Config');
        $this->user = Session::getLoggedinUser();
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getList($limit = 12, $offset = "")
    {
        //@todo review for scalability. currently for pagination we need to load all conversation guids/time
        $conversations = $this->db->get("object:gathering:conversations:{$this->user->guid}", ['limit'=>10000]);
        if($conversations){
            $return = [];

            arsort($conversations);
            $i = 0;
            $ready = false;
            foreach($conversations as $guid => $data){

                if(!$ready && $offset){
                    if($guid == $offset)
                        $ready = true;
                    continue;
                }

                if((string) $guid === (string) Session::getLoggedinUser()->guid)
                    continue;

                if(($i++ > 12 && !$offset) || ($i++ > 24))
                    continue;

                if($guid == $offset){
                    unset($conversations[$guid]);
                    continue;
                }

                if(is_numeric($data)){
                    $data = [
                      'ts' => $data,
                      'unread' => 0
                    ];
                } else {
                    $data = json_decode($data, true);
                }

                $conversation = new Messenger\Entities\Conversation();
                $conversation->loadFromArray($data);
                //$conversation->setGuid($guid);
                if(strpos($guid, ':') === FALSE){
                    $conversation->clearParticipants();
                    $conversation->setParticipant(Session::getLoggedinUser()->guid)
                        ->setParticipant($guid);
                } else {
                    $conversation->setGuid($guid);
                }


                $return[] = $conversation;
                continue;
            }
        }
        $return = $this->filterOnline($return);
        return $return;
    }

    public function filterOnline($conversations)
    {
        $config = $this->config->get('redis');
        $this->redis->connect($config['pubsub'] ?: $config['master'] ?: '127.0.0.1');
        //put this set of conversations into redis
        $guids = [];
        foreach($conversations as $conversation){
            foreach($conversation->getParticipants() as $participant){
                if($participant != Session::getLoggedInUserGuid())
                    $guids[$participant] = $participant;
            }
        }
        array_unshift($guids, Session::getLoggedInUserGuid() . ":conversations");
        call_user_func_array([$this->redis, 'sadd'], $guids);

        //return the online users
        $online = $this->redis->sinter("online", Session::getLoggedInUserGuid() . ":conversations");

        foreach($conversations as $key => $conversation){
            foreach($conversation->getParticipants() as $participant){
                if(in_array($participant, $online)){
                    $conversations[$key] = $conversation->setOnline(true);
                }
            }
        }

        return $conversations;
    }

}
