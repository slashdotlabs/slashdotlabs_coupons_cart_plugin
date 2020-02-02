<?php

namespace Slash\Api;


use Slash\Base\BaseController;

class SettingsApi extends BaseController
{
    public $admin_pages = [];
    public $admin_subpages = [];
    public $settings = [];
    public $sections = [];
    public $fields = [];

    public function register()
    {
        if (!empty($this->admin_pages) || !empty($this->admin_subpages)) {
            add_action('admin_menu', [$this, 'addAdminMenu']);
        }

        if(!empty($this->settings)){
            add_action('admin_init', [$this, 'registerCustomFields']);
        }
    }


    public function addPages(Array $pages)
    {
        $this->admin_pages = $pages;
        return $this;
    }

    /**
     * @param null|string $title
     * @return $this
     */
    public function withSubPage($title = null)
    {
        if (empty($this->admin_pages)) {
            return $this;
        }

        $admin_page = $this->admin_pages[0];
        $subpages = [
            [
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => $title ?: $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback']
            ]
        ];
        $this->admin_subpages = $subpages;
        return $this;
    }

    public function addSubPages(array $pages)
    {
        $this->admin_subpages = array_merge($this->admin_subpages, $pages);
        return $this;
    }

    public function addAdminMenu()
    {
        foreach ($this->admin_pages as $page)
        {
            add_menu_page(
                $page['page_title'],
                $page['menu_title'],
                $page['capability'],
                $page['menu_slug'],
                $page['callback'],
                $page['icon_url'],
                $page['position']
            );
        }

        foreach ($this->admin_subpages as $page)
        {
            add_submenu_page(
                $page['parent_slug'],
                $page['page_title'],
                $page['menu_title'],
                $page['capability'],
                $page['menu_slug'],
                $page['callback']
            );
        }
    }

    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    public function setSections(array $sections)
    {
        $this->sections = $sections;
        return $this;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function  registerCustomFields()
    {
        foreach ($this->settings as $setting)
        {
            // register setting
            register_setting($setting['option_group'], $setting['option_name'], $setting['callback'] ?? '');
        }

        foreach ($this->sections as $section) {
            // add settings section
            add_settings_section($section['id'], $section['title'], $section['callback'] ?? '', $section['page']);
        }

        foreach ($this->fields as $field){
            // add setting field
            add_settings_field($field['id'], $field['title'], $field['callback'] ?? '', $field['page'], $field['section'], $field['args'] ?? '');
        }

    }

}