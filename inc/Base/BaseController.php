<?php


namespace Slash\Base;


use Slash\Api\Twig;

abstract class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $plugin_name;
    public $twig;

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin_name = SLASH_COUPON_PLUGIN_NAME;

        $this->twig = Twig::instance();
    }

}