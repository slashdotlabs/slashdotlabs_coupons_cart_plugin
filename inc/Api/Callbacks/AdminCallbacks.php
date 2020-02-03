<?php


namespace Slash\Api\Callbacks;


use Slash\Base\BaseController;

class AdminCallbacks extends BaseController
{
    public function adminDashboard()
    {
        require_once $this->plugin_path . 'templates/admin.php';
    }

    public function confSettings()
    {

        return require_once("$this->plugin_path/templates/settings.php");
    }

    public function paymentsPage()
    {

        return require_once("$this->plugin_path/templates/payments.php");
    }

    public function ccartSettingsGroup($input)
    {
        return $input;
    }

    public function ccartIpaySection()
    {
        echo '<i>Enter the Vendor ID and Hashkey issued by iPay.</i>';
    }

    public function ccartVendorId()
    {
        $value = esc_attr(get_option('vendor_id'));
        echo '<input type="text" class ="regular-text" 
            name="vendor_id" value=" ' . $value . '" 
            placeholder="Enter iPay Vendor ID" required>';

    }

    public function ccartHashkey()
    {
        $value = esc_attr(get_option('hashkey'));
        echo '<input type="text" class ="regular-text" 
            name="hashkey" value=" ' . $value . '" 
            placeholder="Enter iPay Hashkey" required>';

    }

    public function ccartMailSection()
    {
        echo '<i>Enter the username and email address that customers will receive emails from.</i>';
    }

    public function ccartAddressFromEmail()
    {
        $value = esc_attr(get_option('address_from_email'));
        echo '<input type="email" class ="regular-text" 
            name="address_from_email" value=" ' . $value . '" 
            placeholder="Enter Sender Email Address" required>';

    }

    public function ccartAddressFromName()
    {
        $value = esc_attr(get_option('address_from_name'));
        echo '<input type="text" class ="regular-text" 
            name="address_from_name" value=" ' . $value . '" 
            placeholder="Enter Email Sender Name" required>';

    }
}