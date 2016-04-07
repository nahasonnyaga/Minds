<?php
/**
* Group entity
*/
namespace Minds\Plugin\Groups\Entities;

use Minds\Core;
use Minds\Core\Di\Di;
use Minds\Core\Guid;
use Minds\Entities\Factory as EntitiesFactory;
use Minds\Entities\NormalizedEntity;
use Minds\Plugin\Groups\Core\Membership;
use Minds\Plugin\Groups\Core\Invitations;
use Minds\Plugin\Groups\Core\Group as CoreGroup;
use Minds\Plugin\Groups\Core\Management;

class Group extends NormalizedEntity
{

    protected $type = 'group';
    protected $guid;
    protected $ownerObj;
    protected $owner_guid;
    protected $name;
    protected $brief_description;
    protected $access_id = 2;
    protected $membership;
    protected $banner = false;
    protected $banner_position;
    protected $icon_time;
    protected $featured = 0;
    protected $featured_id;
    protected $tags = '';
    protected $owner_guids = [];

    protected $exportableDefaults = [
        'guid',
        'type',
        'name',
        'brief_description',
        'icon_time',
        'banner',
        'banner_position',
        'membership',
        'featured',
        'featured_id',
        'tags',
    ];

    /**
     * Save
     * @return boolean
     */
    public function save()
    {
        if (!$this->guid) {
            $this->guid = Guid::build();
            $this->time_created = time();
        }

        $saved = $this->saveToDb([
            'type' => $this->type,
            'guid' => $this->guid,
            'owner_guid' => $this->owner_guid,
            'ownerObj' => $this->ownerObj->export(),
            'name' => $this->name,
            'brief_description' => $this->brief_description,
            'access_id' => $this->access_id,
            'membership' => $this->membership,
            'banner' => $this->banner,
            'banner_position' => $this->banner_position,
            'icon_time' => $this->icon_time,
            'featured' => $this->featured,
            'featured_id' => $this->featured_id,
            'tags' => $this->tags,
            'owner_guids' => $this->owner_guids,
        ]);

        if (!$saved) {
            throw new \Exception("We couldn't save the entity to the database");
        }

        $this->saveToIndex();
        \elgg_trigger_event('create', $this->type, $this);

        return $this;
    }

    /**
     * Deletes from DB
     * @return boolean
     */
    public function delete()
    {

        $this->unFeature();

        Di::get('Queue')
          ->setExchange('mindsqueue')
          ->setQueue('FeedCleanup')
          ->send([
              'guid' => $this->getGuid(),
              'owner_guid' => $this->getOwnerObj()->guid,
              'type' => $this->getType()
          ]);

        Di::get('Queue')
          ->setExchange('mindsqueue')
          ->setQueue('CleanupDispatcher')
          ->send([
              'type' => 'group',
              'group' => $this->export()
          ]);

        $db = new \Minds\Core\Data\Call('entities');

        return (bool) $db->removeRow($this->getGuid());
    }

    /**
     * Compatibility getter
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'guid') {
            return $this->getGuid();
        } elseif ($name === 'type') {
            return $this->getType();
        } elseif ($name === 'container_guid') {
            return null;
        } elseif ($name === 'owner_guid') {
            return $this->getOwnerObj() ? $this->getOwnerObj()->guid : $this->owner_guid;
        }

        return null;
    }

    /**
     * Sets `ownerObj`
     * @param Entity $ownerObj
     * @return Group
     */
    public function setOwnerObj($ownerObj)
    {
        if (is_array($ownerObj)) {
            $ownerObj = EntitiesFactory::build($ownerObj['guid']);
        }

        $this->ownerObj = $ownerObj;

        return $this;
    }

    /**
     * Sets `brief_description`
     * @param mixed $brief_description
     * @return Group
     */
    public function setBriefDescription($brief_description)
    {
        $this->brief_description = $brief_description;

        return $this;
    }

    /**
     * Gets `brief_description`
     * @return mixed
     */
    public function getBriefDescription()
    {
        return $this->brief_description;
    }

    /**
     * Sets `access_id`
     * @param mixed $access_id
     * @return Group
     */
    public function setAccessId($access_id)
    {
        $this->access_id = $access_id;

        return $this;
    }

    /**
     * Gets `access_id`
     * @return mixed
     */
    public function getAccessId()
    {
        return $this->access_id;
    }

    /**
     * Sets `banner_position`
     * @param mixed $banner_position
     * @return Group
     */
    public function setBannerPosition($banner_position)
    {
        $this->banner_position = $banner_position;

        return $this;
    }

    /**
     * Gets `banner_position`
     * @return mixed
     */
    public function getBannerPosition()
    {
        return $this->banner_position;
    }

    /**
     * Sets `icon_time`
     * @param mixed $icon_time
     * @return Group
     */
    public function setIconTime($icon_time)
    {
        $this->icon_time = $icon_time;

        return $this;
    }

    /**
     * Gets `icon_time`
     * @return mixed
     */
    public function getIconTime()
    {
        return $this->icon_time;
    }

    /**
     * Sets `owner_guids`
     * @param array $owner_guids
     * @return Group
     */
    public function setOwnerGuids(array $owner_guids)
    {
        $this->owner_guids = array_filter(array_unique($owner_guids), [ $this, 'isValidOwnerGuid' ]);

        return $this;
    }

    /**
     * Returns if a GUID is valid. Used internally.
     * @param  mixed  $guid
     * @return boolean
     */
    public function isValidOwnerGuid($guid)
    {
        return (bool) $guid && (is_numeric($guid) || is_string($guid));
    }

    /**
     * Gets `owner_guids`
     * @return mixed
     */
    public function getOwnerGuids()
    {
        if(empty($this->owner_guids) && $this->owner_guid){
            $this->owner_guids[] = $this->owner_guid;
        }
        return $this->owner_guids ?: [];
    }

    /**
     * Push a new GUID onto `owner_guids`
     * @param mixed $guid
     * @return Group
     */
    public function pushOwnerGuid($guid)
    {
        return $this->setOwnerGuids(array_merge($this->getOwnerGuids(), [ $guid ]));
    }

    /**
     * Remove a GUID from `owner_guids`
     * @param mixed $guid
     * @return Group
     */
    public function removeOwnerGuid($guid)
    {
        return $this->setOwnerGuids(array_diff($this->getOwnerGuids(), [ $guid ]));
    }

    /**
     * Checks if a user is member of this group
     * @param  User    $user
     * @return boolean
     */
    public function isMember($user = null)
    {
        return (new Membership)->setGroup($this)
          ->isMember($user);
    }

    /**
     * Checks if a user has a membership request for this group
     * @param  User    $user
     * @return boolean
     */
    public function isAwaiting($user = null)
    {
        return (new Membership)->setGroup($this)
          ->isAwaiting($user);
    }

    /**
     * Checks if a user is invited to this group
     * @param  User    $user
     * @return boolean
     */
    public function isInvited($user = null)
    {
        return (new Invitations)->setGroup($this)
          ->isInvited($user);
    }

    /**
     * Checks if a user is banned from this group
     * @param  User    $user
     * @return boolean
     */
    public function isBanned($user = null)
    {
        return (new Membership($this))->isInvited($user);
    }

    /**
     * Checks if a user can edit this group
     * @param  User    $user
     * @return boolean
     */
    public function isOwner($user = null)
    {
        if (!$user) {
            return false;
        }

        if ($this->isCreator($user)) {
            return true;
        }

        $user_guid = is_object($user) ? $user->guid : $user;

        return $this->isCreator($user) || in_array($user_guid, $this->getOwnerGuids());
    }

    /**
     * Checks if a user is the group's creator
     * @param  User    $user
     * @return boolean
     */
    public function isCreator($user = null)
    {
        if (!$user) {
            return false;
        }

        $user_guid = is_object($user) ? $user->guid : $user;
        $owner = $this->getOwnerObj();

        if (!$owner) {
            return false;
        }

        return $user_guid == $owner->guid;
    }

    /**
     * Checks if the group is open and public
     * @return boolean
     */
    public function isPublic()
    {
        return $this->getMembership() == 2;
    }

    /**
     * Attaches a user to this group
     * @param  User   $user
     * @return boolean
     */
    public function join($user = null, array $opts = [])
    {
        return (new Membership)->setGroup($this)
          ->setActor($user)
          ->join($user, $opts);
    }

    /**
     * Removes a user from this group
     * @param  User   $user
     * @return boolean
     */
    public function leave($user = null)
    {
        return (new Membership)->setGroup($this)
          ->setActor($user)
          ->leave($user);
    }

    public function getMembersCount()
    {
        $relDb = Di::_()->get('Database\Cassandra\Relationships');
        $relDb->setGuid($this->getGuid());
        return $relDb->countInverse('member');
    }

    public function getActivityCount()
    {
        $indexes = Di::_()->get('Database\Cassandra\Indexes');
        return $indexes->count("activity:container:{$this->getGuid()}");
    }

    /**
     * Public facing properties export
     * @param  array  $keys
     * @return array
     */
    public function export(array $keys = [])
    {
        $export = parent::export($keys);

        // Compatibility keys
        $export['owner_guid'] = $this->getOwnerObj()->guid;
        $export['activity:count'] = $this->getActivityCount();
        $export['members:count'] = $this->getMembersCount();
        $export['icontime'] = $export['icon_time'];
        $export['briefdescription'] = $export['brief_description'];

        $export['is:owner'] = $this->isOwner(Core\Session::getLoggedInUser());
        $export['is:member'] = $this->isMember(Core\Session::getLoggedInUser());
        $export['is:creator'] = $this->isCreator(Core\Session::getLoggedInUser());
        $export['is:awaiting'] = $this->isAwaiting(Core\Session::getLoggedInUser());

        return $export;
    }
}
