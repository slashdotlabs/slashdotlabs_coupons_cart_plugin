<?php


namespace Slash\Api\Callbacks;

use Slash\Base\BaseController;
use Slash\Database\PaymentsModel;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminCallbacks extends BaseController
{
    public function confSettings()
    {

        return require_once("$this->plugin_path/templates/settings.php");
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function paymentsPage()
    {

        $paymentModel = new PaymentsModel();
        $payments = $paymentModel->fetchPayments();
        $status_badges_map = [
            "initiated" => "info",
            "completed" => "success",
            "cancelled" => "danger"
        ];
        echo $this->twig->render('payments.twig', ['payments' => $payments, 'status_badges_map' => $status_badges_map]);
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

    public function ccartSettingsValidate($input)
    {
        $output = get_option('coupons_plugin');

        $output["ipay"]["live"] = isset($input["ipay"]["live"]) ? true : false;
        $output["ipay"]["vendor_id"] = $input["ipay"]["vendor_id"];
        $output["ipay"]["hashkey"] = $input["ipay"]["hashkey"];

        $output["mail"]["address_from_name"] = $input["mail"]["address_from_name"];
        is_email($input['mail']['address_from_email']) ? $output['mail']['address_from_email'] = $input['mail']['address_from_email'] :
            add_settings_error('coupons_plugin', 'invalid-email', 'Enter a valid e-mail address.');

        $output["smtp"]["server"] = $input["smtp"]["server"];
        $output["smtp"]["port"] = $input['smtp']['port'];
        $output["smtp"]["encryption"] = $input["smtp"]["encryption"];
        $output["smtp"]["username"] = $input["smtp"]["username"];
        $output["smtp"]["password"] = $input["smtp"]["password"];
        $output["smtp"]["settings"] = isset($input["smtp"]["settings"]) ? true : false;
        return $output;


    }

    public function ccartCheckboxFields($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $class = $args['class'];
        $checkbox = get_option($option_name);
        $value = esc_attr($checkbox[$field][$name] ?? '');
        echo '
            <input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $field . '][' . $name . ']" class="' . $class . '"  value = "1" ' . (($value == '1') ? 'checked' : '') . '>
        ';
    }

    public function ccartTextFields($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $placeholder = $args['placeholder'];
        $settings = (array)get_option($option_name);
        $value = esc_attr($settings[$field][$name] ?? '');
        echo '
            <input type="text" id="' . $name . '" name="' . $option_name . '[' . $field . '][' . $name . ']" placeholder="' . $placeholder . '" value= "' . $value . '" required>
        ';
    }

    public function ccartNumericFields($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $placeholder = $args['placeholder'];
        $settings = (array)get_option($option_name);
        $value = esc_attr($settings[$field][$name] ?? '');
        echo '
            <input type="number" id="' . $name . '" name="' . $option_name . '[' . $field . '][' . $name . ']" placeholder="' . $placeholder . '" value= "' . $value . '" required>
        ';
    }

    public function ccartRadioButtons($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $placeholder = $args['placeholder'];

        $settings = (array)get_option($option_name);
        $value = esc_attr($settings[$field][$name] ?? '');

        echo '
            <input type="radio" id="' . $name . '" name="' . $option_name . '[' . $field . '][' . $name . ']" placeholder="' . $placeholder . '" value= "ssl" ' . (($value == 'ssl') ? 'checked' : '') . '> SSL 
            &ensp;
            <input type="radio" id="' . $name . '" name="' . $option_name . '[' . $field . '][' . $name . ']" placeholder="' . $placeholder . '" value= "tls" ' . (($value == 'tls') ? 'checked' : '') . '> TLS
        ';
    }

    public function ccartPasswordFields($args)
    {
        $name = $args['label_for'];
        $field = $args['field'];
        $option_name = $args['option_name'];
        $placeholder = $args['placeholder'];
        $settings = (array)get_option($option_name);
        $value = esc_attr($settings[$field][$name] ?? '');
        echo '
            <input type="password" id="' . $name . '" name="' . $option_name . '[' . $field . '][' . $name . ']" placeholder="' . $placeholder . '" value= "' . $value . '" required>
        ';
    }


}
