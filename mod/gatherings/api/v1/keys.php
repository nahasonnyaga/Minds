<?php
/**
 * Minds Newsfeed API
 * 
 * @version 1
 * @author Mark Harding
 */
namespace minds\plugin\gatherings\api\v1;

use Minds\Core;
use minds\plugin\gatherings\entities;
use minds\plugin\gatherings\helpers;
use minds\interfaces;
use Minds\Api\Factory;

class keys implements interfaces\api{

    /**
     * Returns the private key belonging to a user
     * @param array $pages
     * 
     * API:: /v1/keys
     */      
    public function get($pages){

       // $_SESSION['user'] = new \Minds\entities\user($_SESSION['user']->guid, false);        
        $unlock_password = get_input('password');
        $new_password = get_input('new_password');
        $tmp = helpers\openssl::temporaryPrivateKey(\elgg_get_plugin_user_setting('privatekey', elgg_get_logged_in_user_guid(), 'gatherings'), $unlock_password, NULL);
        $pub = \elgg_get_plugin_user_setting('publickey', elgg_get_logged_in_user_guid(), 'gatherings');
       
	    if($tmp){
            $response['key'] = $tmp;
        } else {
            $response['status'] = 'error';
            $response['message'] = "please check your password";
        }
    
        return Factory::response($response);
        
    }
    
    public function post($pages){

        switch($pages[0]){
            case "setup":
                $keypair = \Minds\plugin\gatherings\helpers\openssl::newKeypair(get_input('passphrase'));
                error_log(print_r($_POST,true));
                \elgg_set_plugin_user_setting('publickey', $keypair['public'], elgg_get_logged_in_user_guid(), 'gatherings');
                \elgg_set_plugin_user_setting('option', '1', elgg_get_logged_in_user_guid(), 'gatherings');
                \elgg_set_plugin_user_setting('privatekey', $keypair['private'], elgg_get_logged_in_user_guid(), 'gatherings');
         
                 $tmp = helpers\openssl::temporaryPrivateKey($keypair['private'], get_input('passphrase'), NULL);
                 $response['key'] = $tmp;             
 
                break;
            case "unlock":
            default:

                $unlock_password = get_input('password');
                $new_password = get_input('new_password');
                $tmp = helpers\openssl::temporaryPrivateKey(\elgg_get_plugin_user_setting('privatekey', elgg_get_logged_in_user_guid(), 'gatherings'), $unlock_password, NULL);
                $pub = \elgg_get_plugin_user_setting('publickey', elgg_get_logged_in_user_guid(), 'gatherings');
              
                $enc = base64_encode(helpers\openssl::encrypt("hello", $pub));
               // $tmp = helpers\openssl::decrypt(base64_decode($enc), $tmp);
                
                if($tmp){
                    $response['key'] = $tmp;
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "please check your password";
                }
        }   
 
        return Factory::response($response);  
       
        
    }
    
    public function put($pages){
        
        return Factory::response(array());
        
    }
    
    public function delete($pages){
        
        return Factory::response(array());
        
    }
    
}
        
