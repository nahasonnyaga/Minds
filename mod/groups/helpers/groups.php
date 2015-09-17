<?php
namespace Minds\plugin\groups\helpers;

use Minds\Core\Data;
use Minds\Core;

class Groups{

  /**
   * Get groups a user is a member of
   */
  static public function getGroups($user, $options = array()){

    if(!$user)
      $user = Core\session::getLoggedInUser();

    $options = array_merge(array(
      'limit' => 12,
      'offset' => ""
    ), $options);

    $key = "$user->guid:member";

    $db = new Data\Call('relationships');
    $guids = $db->getRow($key, array('offset'=>$options['offset'], 'limit'=>$options['limit']));

    if(!$guids)
      return array();

    $groups = Core\Entities::get(array('guids'=>array_keys($guids)));

    return $groups;
  }

}
