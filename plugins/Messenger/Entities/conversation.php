<?php
/**
 * Messenger Conversation
 */

namespace Minds\Plugin\Messenger\Entities;

use Minds\Core\Session;
use Minds\Core\Di\Di;
use Minds\Entities\DenormalizedEntity;
use Minds\Entities\User;

class Conversation extends DenormalizedEntity{

	protected $rowKey;

	protected $exportableDefaults = [
		'guid', 'type', 'subtype'
	];
	protected $type = 'messenger';
	protected $subtype = 'conversation';
	protected $guid;
	protected $ts;
	protected $unread = 0;
	protected $participants = [];

	public function __construct($db = NULL)
	{
			parent::__construct($db);
			$this->rowKey = "object:gathering:conversations:" . Session::getLoggedInUser()->guid;
	}

	public function setParticipant($guid)
	{
			if($guid instanceof User){
					$guid = $guid->user;
			}
			if(!isset($this->participants[$guid])){
					$this->participants[$guid] = $guid;
			}
			return $this;
	}

	public function getParticipants()
	{
			return $this->participants ?: [];
	}

	public function getGuid()
	{
			if($this->guid){
					return $this->guid;
			}
			return $this->permutateGuid($this->getParticipants());
	}

	public function buildSocketRoomName()
	{
		if (strpos($this->getGuid(), ':') !== false) {
			return 'conversation:' . $this->getGuid();
		}

		// Fallback
		return 'conversation:' . $this->permutateGuid($this->getParticipants());
	}

	public function setGuid($guid)
	{
			$this->guid = $guid;

			if (strpos($guid, ':') !== false) {
				$participants = explode(':', $guid);
				foreach($participants as $participant){
					$this->setParticipant($participant);
				}
			}
	}

	private function permutateGuid($input = [])
	{
			$result = "";
			ksort($input);
			foreach($input as $key => $item){
					$result .= $result ? ":$key" : $key;
			}
			return $result;
	}

	public function saveToLists()
	{
			foreach($this->participants as $participant_guid => $participant){
					$this->db->insert("object:gathering:conversations:$participant_guid", [
						$this->getGuid() => json_encode([
							'ts' => time(),
							'unread' => 0,
							'participants' => array_values($this->participants)
						])
					]);
			}
	}

	public function export($keys = [])
	{
			$export = parent::export($keys);

			foreach($this->participants as $user_guid){
					if($user_guid != Session::getLoggedinUser()->guid){
							$user = new User($user_guid);
							$export['participants'][$user_guid] = $user->export();
							//$export['guid'] = (string) $user_guid; //for legacy support
							$export['name'] = $user->name;
							$export['username'] = $user->username;
					}
			}
			$export['participants'] = array_values($export['participants']); //make sure we are an array, not an object
			$export['socketRoomName'] = $this->buildSocketRoomName();

			return $export;
	}

}
