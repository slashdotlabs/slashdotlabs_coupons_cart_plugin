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

    public function ccartSettingsSanitize( $input )
	{
        return $input;
	}

    public function ccartIpaySection(){
        echo '<i>Enter the Vendor ID and Hashkey issued by iPay.</i>';
    }
    public function ccartMailSection(){
        echo '<i>Enter the username and email address that customers will receive emails from.</i>';
    }

    public function ccartSettingsFields($args){
        {
            $name = $args['label_for'];
            $field = $args['field'];
            $classes = $args['class'];
            $option_name = $args['option_name'];
            $placeholder = $args['placeholder'];

            echo '
            <div class ="'.$classes.'">
                <input type="text" name="'. $option_name . '['. $field . ']['. $name .']" placeholder="'. $placeholder .'" value= "" required>
            <div>';
            }
        }
}
