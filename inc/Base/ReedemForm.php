<?php


namespace Slash\Base;


use Slash\Api\IpayGateway;
use Slash\Base\BaseController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ReedemForm extends BaseController
{

    public function register()
    {
        // shortcode
        add_shortcode('slash_coupon_redeem_form', [$this, 'activate']);

        // Hook form on every post display
        add_filter('the_content', [$this, 'hook_redeem_form']);

        // Add ajax handler both
        add_action('wp_ajax_redeem_coupon', [$this, 'redeem_coupon']);
        add_action('wp_ajax_nopriv_redeem_coupon', [$this, 'redeem_coupon']);

    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    function activate()
    {
        echo $this->twig->render('shortcodes/redeem_form.twig');
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

        // Insert transaction record in payments table TODO:


        // Get iframe URL for iPay gateway
        $meta_data = [
            'order_id' => "ccart-demo-".time(),
            'email' => $data['customer_email'],
            'phone_number' => $data['customer_phone_number'],
            'total_amount' => $data['coupon_price'],
            'cbk' => get_permalink($data['coupon_id']),
            'lbk' => get_permalink($data['coupon_id']),
        ];
        $iframeURL = IpayGateway::retriveUrl($meta_data);
        if (!$iframeURL) wp_send_json_error(['msg' => 'Could not process request']);
        wp_send_json_success(['iframeURL' => $iframeURL]);
        wp_die();
    }
}