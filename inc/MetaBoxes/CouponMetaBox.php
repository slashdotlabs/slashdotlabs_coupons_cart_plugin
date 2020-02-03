<?php


namespace Slash\MetaBoxes;


use Slash\Base\BaseController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CouponMetaBox extends BaseController
{
    public function register()
    {
        // Add meta box to UI
        add_action('add_meta_boxes', [$this, 'activate']);

        // Add save callback to save post hook
        add_action('save_post', [$this, 'updateValues']);
    }

    public function activate()
    {
        add_meta_box(
            'slash_coupon_metabox',
            'COUPON DETAILS',
            [$this, 'couponMetaBoxContent'],
            'post',
            'normal'
        );
    }

    public function updateValues($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return true;
        if ($parent_id = wp_is_post_revision($post_id)) {
            $post_id = $parent_id;
        }

        $fields = ['coupon_name', 'coupon_price', 'coupon_expiry_date'];
        $coupon_data = array_filter($_POST, function ($key) use ($fields) {
            return in_array($key, $fields);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($coupon_data)) return true;
        $errors = $this->validate_input($coupon_data);

        // TODO: sanitize input

        if(!empty($errors)){
            // TODO: Handle errors
            var_dump($errors);
        }

        update_post_meta($post_id, 'slash_coupon_data', $coupon_data);
        return true;
    }

    public function validate_input($data)
    {
        $errors = [];
        $messages = [
            'coupon_name' => [
                'required' => 'Coupon name is required',
            ],
            'coupon_price' => [
                'required' => 'Coupon price is required',
                'min' => 'Coupon price must be 0 or greater'
            ],
            'coupon_expiry_date' => [
                'required' => 'Coupon expiry date is required',
                'before_today' => 'Coupon expiry date should be greater than today'
            ]
        ];
        // Check empty values
        foreach ($data as $key => $value) {
            if (empty($value) || $value === "") {
                $errors[$key][] = $messages[$key]['required'];
            }
        }
        return $errors;
    }

    /**
     *  Show coupon form
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function couponMetaBoxContent()
    {
        $coupon_data = get_post_meta(get_the_ID(), 'slash_coupon_data');
        echo $this->twig->render('coupon_metabox.twig', [
            'data' => $coupon_data[0] ?? [],
            'today' => date('Y-m-d')
        ]);
    }
}