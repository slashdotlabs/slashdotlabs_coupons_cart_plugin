<?php
/**
 * @package       CouponsCartPlugin
 */

namespace Slash\Pages;

use \Slash\Api\SettingsApi;
use \Slash\Base\BaseController;
use \Slash\Api\Callbacks\SubmissionCallbacks;



class Submissions extends BaseController
{
	public $settings;

	public $callbacks;

	public $pages = array();
	
	public function register()
	{
		$this->settings = new SettingsApi();

		$this->callbacks = new SubmissionCallbacks();

		$this->setPages();

		$this->settings->addPages($this->pages)->register();

	}

	public function setPages()
	{
		$this->pages = array(
			array(
				'page_title' => 'CouponsCartPlugin',
				'menu_title' => 'Payments',
				'capability' => 'manage_options',
				'menu_slug' => 'coupons_plugin',
				'callback' => array($this->callbacks, 'submissionDashboard'),
				'icon_url' => 'dashicons-store',
				'position' => 100
			)
		);
	}

}