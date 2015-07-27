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
                Helpers\Analytics::increment("active");
            break;
        }

        return Factory::response(array());
    }

    public function delete($pages){
    }

}
