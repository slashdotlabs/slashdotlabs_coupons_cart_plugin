<?php


namespace Slash\Api\Callbacks;


use Slash\Base\BaseController;

class AdminCallbacks extends BaseController
{
    public function adminDashboard()
    {
        require_once $this->plugin_path . 'templates/admin.php';
    }
}