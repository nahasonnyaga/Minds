<?php
/**
 * Minds Group API
 *
 * @version 1
 * @author Mark Harding
 */
namespace minds\plugin\groups\api\v1;

use Minds\Core;
use Minds\plugin\groups\entities;
use Minds\plugin\groups\helpers;
use Minds\Interfaces;
use Minds\Api\Factory;
use Minds\Entities as CoreEntities;

class group implements Interfaces\Api{

    /**
     * Returns the conversations or conversation
     * @param array $pages
     *
     * API:: /v1/group/group/:guid
     */
    public function get($pages){

        $group = new entities\Group($pages[0]);
        $response['group'] = $group->export();
        $response['group']['members'] = Factory::exportable(helpers\Membership::getMembers($group));
        $response['group']['members:count'] = helpers\Membership::getMembersCount($group);
        $response['group']['requests'] = array();
        $response['group']['requests:count'] = helpers\Membership::getRequestsCount($group);

        return Factory::response($response);

    }

    public function post($pages){

      if(isset($pages[0])){
        $group = new entities\Group($pages[0]);
      } else {
        $group = new entities\Group();
      }

      if(isset($pages[1]) && $group->guid){
        $response = array();
        switch($pages[1]){
          case "avatar":
            break;
          case "banner":
            if(is_uploaded_file($_FILES['file']['tmp_name'])){
              $resized = get_resized_image_from_uploaded_file('file', 2000);
              $file = new CoreEntities\File();
              $file->owner_guid = $group->owner_guid;
              $file->setFilename("group/{$group->guid}.jpg");
              $file->open('write');
              $file->write($resized);
              $file->close();
              $group->banner = true;
              $group->banner_position = $_POST['banner_position'];
              $group->save();
            }
            break;
          }
          return Factory::response($response);
      }

      $group->name = $_POST['name'];
      $group->access_id = 2;
      $group->membership = $_POST['membership'];
      $group->save();
      //now join
      $group->join(Core\Session::getLoggedInUser());

      $response = array();
      $response['guid'] = $group->guid;

      return Factory::response($response);
    }

    public function put($pages){
        return Factory::response(array());
    }

    public function delete($pages){
        return Factory::response(array());
    }

}
