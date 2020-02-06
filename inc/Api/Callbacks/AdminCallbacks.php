<?php


namespace Slash\Api\Callbacks;


use Slash\Base\BaseController;

class AdminCallbacks extends BaseController{
    public function confSettings(){

        return require_once("$this->plugin_path/templates/settings.php");
    }

    public function paymentsPage()
    {
        return require_once("$this->plugin_path/templates/payments.php");
    }

    // public function ccartSettingsActivate( $input )
    // {
    //     $output = array();
    //     $ipay_value = $this->ccart_settings["ipay"][2];
    //     $output[$ipay_value] = isset( $input[$ipay_value] ) ? true : NULL;
    //     $smtp_value = $this->ccart_settings["smtp"][2];
    //     $output[$smtp_value] = isset( $input[$smtp_value] ) ? true : NULL;

    //     return $output; }

    public function ccartIpaySection()
    {
        echo '<i>Enter the Vendor ID and Hashkey issued by iPay.</i>';
    }
    public function ccartMailSection()
    {
        echo '<i>Enter the username and email address that customers will receive emails from.</i>';
    }
   
    public function ccartSMTPSection()
    {
        echo '<i>Enter the Mail Server SMTP Settings.</i>';
    }

    public function ccartSettingsValidate ($input)
    {
        $output = get_option( 'coupons_plugin' );
        
        $output["ipay"]["live"] = isset( $input["ipay"]["live"] ) ? true : false;
        $output["ipay"]["vendor_id"] = $input["ipay"]["vendor_id"];
        $output["ipay"]["hashkey"] = $input["ipay"]["hashkey"];
        
        $output["mail"]["address_from_name"] = $input["mail"]["address_from_name"];
        is_email( $input['mail']['address_from_email'])  ? $output['mail']['address_from_email']  = $input['mail']['address_from_email']:
            add_settings_error( 'coupons_plugin', 'invalid-email', 'Enter a valid e-mail address.' );
        
        $output["smtp"]["server"] = $input["smtp"]["server"];
        is_numeric( $input['smtp']['port'] ) ? $output["smtp"]["port"]  = $input['smtp']['port']:
            add_settings_error( 'coupons_plugin', 'invalid-value', 'Enter a numeric value as the SMTP Port Number.' );
        $output["smtp"]["encryption"] = $input["smtp"]["encryption"];
        $output["smtp"]["username"] = $input["smtp"]["username"];
        $output["smtp"]["password"] = $input["smtp"]["password"];
        $output["smtp"]["settings"] = isset( $input["smtp"]["settings"] ) ? true : false;
        return $output;
    
    
    }
    public function ccartCheckboxFields($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $class = $args['class'];
        $checkbox = get_option($option_name);
        $value = esc_attr( $checkbox[$field][$name] ?? '' );
        echo '
            <input type="checkbox" name="'. $option_name . '['. $field . ']['. $name .']" class="'. $class .'"  value = "1" ' . (($value=='1') ? 'checked' : '') . '>
        ';
    }

    public function ccartTextFields($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $placeholder = $args['placeholder'];
        $settings = (array) get_option($option_name);
        $value = esc_attr( $settings[$field][$name] ?? '');
        echo '
            <input type="text" name="'. $option_name . '['. $field . ']['. $name .']" placeholder="'. $placeholder .'" value= "'.$value.'" required>
        ';
    }

    public function ccartRadioButtons($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $placeholder = $args['placeholder'];

        $settings = (array) get_option($option_name);
        $value = esc_attr( $settings[$field][$name] ?? '' );

        echo '
            <input type="radio" name="'. $option_name . '['. $field . ']['. $name .']" placeholder="'. $placeholder .'" value= "ssl" ' . (($value=='ssl') ? 'checked' : '') . '> SSL 
            &ensp;
            <input type="radio" name="'. $option_name . '['. $field . ']['. $name .']" placeholder="'. $placeholder .'" value= "tls" ' . (($value=='tls') ? 'checked' : '') . '> TLS
        ';
    }
    public function ccartPasswordFields($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $placeholder = $args['placeholder'];
        $settings = (array) get_option($option_name);
        $value = esc_attr( $settings[$field][$name] ?? '' );
        echo '
            <input type="password" name="'. $option_name . '['. $field . ']['. $name .']" placeholder="'. $placeholder .'" value= "'.$value.'" required>
        ';
    }
    

}
