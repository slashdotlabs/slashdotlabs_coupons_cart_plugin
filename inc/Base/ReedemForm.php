<?php


namespace Slash\Base;


use Slash\Api\IpayGateway;
use Slash\Base\BaseController;
use Slash\Database\PaymentsModel;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ReedemForm extends BaseController
{

    public function register()
    {
        // Hook form on every post display
        add_filter('the_content', [$this, 'hook_redeem_form']);

        // Add ajax handler both logged in and non logged in users
        add_action('wp_ajax_redeem_coupon', [$this, 'redeem_coupon']);
        add_action('wp_ajax_nopriv_redeem_coupon', [$this, 'redeem_coupon']);

        // Hook code to handle payment callback on each post page
        add_action('wp_head', [$this, 'payment_cb_handler']);

    }

    /**
     * @param $content
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function hook_redeem_form($content)
    {
        $args = [
            'coupon_id' => get_the_ID(),
            'expired' => true,
        ];
        $coupon_data = get_post_meta(get_the_ID(), 'slash_coupon_data');
        $coupon_data = $coupon_data[0] ?? [];

        if(!empty($coupon_data))
        {
            $args['data'] = $coupon_data;
            $expiry_date = strtotime($coupon_data['coupon_expiry_date']);
            $args['expired'] = ( time() - $expiry_date  ) > 0;
        }
        $form = $this->twig->render('shortcodes/redeem_form.twig', $args);

        if (is_single()) {
            $content .= $form;
        }
        return $content;
    }

    public function redeem_coupon()
    {
        // Verify nonce
        check_ajax_referer('scp_redeem_form');

        // Get form data
        $data = $_POST['data'];

        // Insert transaction record in payments table
        $paymentsModel = new PaymentsModel();
        $coupon_count =  $paymentsModel->getCouponCount($data['coupon_name']);
        $insert_data = [
            'fname' => $data['customer_first_name'],
            'lname' => $data['customer_last_name'],
            'email' => $data['customer_email'],
            'phone' => $data['customer_phone_number'],
            'coupon_id' => $data['coupon_id'],
            'amount' => $data['coupon_price'],
            'customer_coupon' => $data['coupon_name']."-".$coupon_count,
            'order_id' => "ccart-".time()
        ];
        $inserted = $paymentsModel->insert($insert_data);
        if(!$inserted) wp_send_json_error(['msg' => 'Could not process request. Try again later']);

        // Get iframe URL for iPay gateway
        $meta_data = [
            'order_id' => $insert_data['order_id'],
            'email' => $data['customer_email'],
            'phone_number' => $data['customer_phone_number'],
            'total_amount' => $data['coupon_price'],
            'cbk' => get_permalink($data['coupon_id']),
        ];
        $iframeURL = (new IpayGateway())->retriveUrl($meta_data);
        if (!$iframeURL) wp_send_json_error(['msg' => 'Could not process request']);
        wp_send_json_success(['iframeURL' => $iframeURL]);
        wp_die();
    }

    public function payment_cb_handler()
    {
        // Only show on post pages and from ipay
        $valid_ipay_call = array_intersect(['status', 'txncd'], array_keys($_GET));
        if (get_the_ID() && !empty($valid_ipay_call))
        {
            (new IpayGateway())->payment_cb_handler();
        }
    }
}