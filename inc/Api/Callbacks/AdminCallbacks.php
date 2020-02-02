<?php
/**
 * 
 * @package CouponsCartPlugin
 */

namespace Slash\Api\Callbacks;

use Slash\Base\BaseController;

class AdminCallbacks extends BaseController{
    public function confSettings(){
        
        return require_once("$this->plugin_path/templates/settings.php");
    }
    public function paymentsPage(){
        
        return require_once("$this->plugin_path/templates/payments.php");
    }
}