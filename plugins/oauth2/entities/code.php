<?php
/**
 * OAUTH2 code entity
 */

namespace minds\plugin\oauth2\entities;

use Minds\Entities;
use Minds\Core\data;

class code extends Entities\Entity
{
    protected $attributes = array(
        'type' => 'oauth2',
        'subtype' => 'code'
    );
    
    public function __construct($code = null)
    {
        if ($code) {
            $this->load($code);
        }
    }
    
    public function load($code)
    {
        $lookup = new Data\lookup('oauth2:code');
        $guid = $lookup->get($code);
        
        if (!isset($guid[0])) {
            throw new \Exception('Lookup failed');
        }
        
        $db = new Data\Call('entities');
        $data = $db->getRow($guid[0], array('limit'=>200));
        
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }
    
    public function save()
    {
        $guid = parent::save();

        $lookup = new Data\lookup('oauth2:code');
        $lookup->set($this->authorization_code, $guid);
    }
    
    public function delete()
    {
        //parent::delete();
        
        $lookup = new Data\lookup('oauth2:code');
        $lookup->remove($this->authorization_code);
    }
    
    /*
     * Return an array in OAuth2 format
     */
    public function export()
    {
        return array(
            'authorization_code' => $this->authorization_code,
            'client_id'          => $this->client_id,
            'user_id'            => $this->owner_guid,
            'redirect_uri'       => $this->redirect_uri,
            'expires'            => $this->expires,
            'scope'              => $this->scope,
            'entity'             => $this,
        );
    }
}
