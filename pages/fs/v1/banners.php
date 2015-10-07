<?php
/**
 * Minds Banners FS endpoint
 *
 * @version 1
 * @author Mark Harding
 */
namespace minds\pages\fs\v1;

use Minds\Core;
use Minds\Entities;
use Minds\Interfaces;
use Minds\Api\Factory;

class banners implements Interfaces\FS{

  public function get($pages){
    $entity = Entities\Factory::build($pages[0]);

    $filepath = "";
    switch($entity->type){
      case "user":
        $size = isset($pages[1]) ? $pages[1] : 'fat';
        $carousels = Core\Entities::get(array('subtype'=>'carousel', 'owner_guid'=>$entity->guid));
        if($carousels)
          $filepath =  Core\Config::build()->dataroot . 'carousel/' . $carousels[0]->guid . $size;
        break;
      case "object":
        break;
    }

    switch($entity->subtype){
      case "blog":
        $f = new Entities\File();
        $f->owner_guid = $entity->owner_guid;
        $f->setFilename("blog/{$entity->guid}.jpg");
        $filepath = $f->getFilenameOnFilestore();
        break;
      case "cms":
        break;
      case "carousel":
        $size = isset($pages[1]) ? $pages[1] : 'fat';
        $filepath =  Core\Config::build()->dataroot . 'carousel/' . $entity->guid . $size;
        break;
    }

		if(!file_exists($filepath))
		  exit;

    $finfo    = finfo_open(FILEINFO_MIME);
    $mimetype = finfo_file($finfo, $filepath);
    finfo_close($finfo);
    header('Content-Type: '.$mimetype);
    header('Expires: ' . date('r', time() + 864000));
    header("Pragma: public");
    header("Cache-Control: public");
    echo file_get_contents($filepath);
    exit;
  }

}
