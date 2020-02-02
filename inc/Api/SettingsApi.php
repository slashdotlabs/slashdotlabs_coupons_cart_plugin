<?php

namespace Slash\Api;

class SettingsApi
{
	public $submission_pages = array();


	public function register()
	{
		if (!empty($this->submission_pages)) {
			add_action( 'admin_menu', array($this, 'addSubmissionMenu'));
		}
	}

	public function addPages(array $pages)
	{
		$this->submission_pages = $pages;

		return $this;
	}

	public function addSubmissionMenu()
	{
		foreach ($this->submission_pages as $page) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback']);
		}
	}
}