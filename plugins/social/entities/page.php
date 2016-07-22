<?php
/**
 * Section
 */
 
namespace minds\plugin\cms\entities;

use minds\plugin\cms\exceptions;
use Minds\Entities;
use Minds\Core\data;

class page extends Entities\Object
{
    
    /**
     * Initialise attributes
     * @return void
     */
    public function initializeAttributes()
    {
        parent::initializeAttributes();
        $this->attributes = array_merge($this->attributes, array(
            'subtype' => 'cms_page',
            'owner_guid' => elgg_get_logged_in_user_guid(),
            'access_id' => 2, //pages are public,
            'context' => 'footer',
            'uri'=>null
        ));
    }
    
    public function __construct($guid = null)
    {
        if (is_string($guid) && !is_numeric($guid)) {
            $guids = Data\indexes::fetch("object:cms:page:$guid", array('limit'=>1));
            if (!$guids) {
                throw new exceptions\notfound($guid);
            }
            
            $guid = key($guids);
        }
        
        return parent::__construct($guid);
    }
    
    /**
     * Returns an array of indexes into which this entity is stored
     *
     * @param bool $ia - ignore access
     * @return array
     */
    protected function getIndexKeys($ia = false)
    {
        return array(
            "$this->type:cms:page:$this->uri",
        );
    }
    
    public function getURL()
    {
        return elgg_get_site_url() . 'p/'.$this->uri;
    }
    
    public function save($timebased = true)
    {
        $guid = parent::save($timebased);
        
        $lu = new Data\lookup();
        $lu->set("object:cms:menu:$this->context", array($this->uri => "$this->title"));

        return $guid;
    }
    
    public function delete($recursive = true)
    {
        $lu = new Data\lookup();
        $lu->removeColumn("object:cms:menu:$this->context", $this->uri);
                
        return parent::delete($recursive);
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
    
    public function setUri($uri)
    {
        //remove the old path uri
        if ($this->uri && $this->uri != $uri) {
            $lu = new Data\lookup();
            $lu->removeColumn("object:cms:menu:$this->context", $this->uri);
        }
        $this->uri = $uri;
        return $this;
    }
    
    public function setForwarding($url)
    {
        if ($url) {
            $this->forwarding = $url;
        }
        return $this;
    }
}
