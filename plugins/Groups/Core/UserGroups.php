<?php
/**
 * User-facing operations for Groups
 */
namespace Minds\Plugin\Groups\Core;

use Minds\Core\Di\Di;
use Minds\Entities\User;
use Minds\Core\Entities;

class UserGroups
{
    protected $user;
    protected $relDB;

    /**
     * Constructor
     * @param User $user
     */
    public function __construct(User $user, $db = null)
    {
        $this->user = $user;
        $this->relDB = $db ?: Di::_()->get('Database\Cassandra\Relationships');
    }

    /**
     * Get the user's groups
     * @param  array $opts
     * @return array
     */
    public function getGroups(array $opts = [])
    {
        $opts = array_merge([
            'limit' => 12,
            'offset' => '',
            'hydrate' => true
        ], $opts);

        $this->relDB->setGuid($this->user->guid);

        $guids = $this->relDB->get('member', [
            'limit' => $opts['limit'],
            'offset' => $opts['offset']
        ]);

        if (!$guids) {
            return [];
        }

        if (!$opts['hydrate']) {
            return $guids;
        }

        $groups = Entities::get([ 'guids' => $guids ]);

        return $groups;
    }
}
