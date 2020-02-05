<?php


namespace Slash\Api\Callbacks;

use Slash\Database\PaymentsModel;
use Slash\Base\BaseController;

class AdminCallbacks extends BaseController{
    public function confSettings(){

        return require_once("$this->plugin_path/templates/settings.php");
    }

    public function paymentsPage()
    {

        $paymentModel = new PaymentsModel();
        $payments = $paymentModel->fetchPayments();
        echo $this->twig->render('payments.php', ['payments' => $payments]);
    }

    public function ccartSettingsValidate ($input)
    {
        $output = get_option( 'coupons_plugin' );

        if ( is_email( $input['mail']['address_from_email']) 
            && ( is_email( $input['smtp']['smtp_username']) ) )
        {
            $output['mail']['address_from_email']  = $input['mail']['address_from_email'];
            $output['smtp']['smtp_username']  = $input['smtp']['smtp_username'];
            $output['smtp']['smtp_port']  = $input['smtp']['smtp_port'];
        }
        else 
        {
            add_settings_error( 'coupons_plugin', 'invalid-email', 'Enter a valid e-mail address.' );
        }

        if ( is_numeric( $input['smtp']['smtp_port']) )
        {
            $output['smtp']['smtp_port']  = $input['smtp']['smtp_port'];
        }
        else 
        {
            add_settings_error( 'coupons_plugin', 'invalid-value', 'Enter a numeric value as the SMTP Port Number.' );
        }

        return $input;
        return $output;
    }

    public function ccartIpaySection()
    {
        echo '<i>Enter the Vendor ID and Hashkey issued by iPay.</i>';
    }
    public function ccartMailSection(){
        echo '<i>Enter the username and email address that customers will receive emails from.</i>';
    }
    public function ccartSMTPSection(){
        echo '<i>Enter the Mail Server SMTP Settings.</i>';
    }

    public function ccartSettingsFields($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $placeholder = $args['placeholder'];
        $settings = (array) get_option($option_name);
        $value = esc_attr( $settings[$field][$name] );
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
        $value = esc_attr( $settings[$field][$name] );
        echo '
            <input type="checkbox" name="'. $option_name . '['. $field . ']['. $name .']" placeholder="'. $placeholder .'" value= "'.$value.'" required>
        ';
    }

}
