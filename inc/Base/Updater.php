<?php


namespace Slash\Base;


class Updater extends BaseController
{
    private $github_username;
    private $github_repo;
    private $authorize_token; // To access private repos
    private $remote_data;

    private $plugin_data;
    private $active;

    public function __construct()
    {
        parent::__construct();

        $this->github_username = "slashdotlabs";
        $this->github_repo = "slashdotlabs_coupons_cart_plugin";
//        $this->authorize_token = "354d375a926bfcf2ebd58724b52fe4458470be4a"; // Personal access token
    }

    public function register()
    {
        add_action('admin_init', [$this, 'set_plugin_properties']);

        add_filter('pre_set_site_transient_update_plugins', [$this, 'modify_transient'], 10, 1);
        add_filter('plugins_api', [$this, 'plugin_popup'], 10, 3);
        add_filter('upgrader_post_install', [$this, 'after_install'], 10, 3);

        add_action('upgrader_process_complete', [$this, 'after_update'], 10, 2);
    }

    public function set_plugin_properties()
    {
        $this->plugin_data = get_plugin_data($this->plugin_file);
        $this->active = is_plugin_active($this->plugin_name);
    }

    public function modify_transient($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        if ($checked = $transient->checked) { // Did Wordpress check for updates?

            $this->get_remote_data(); // Get the repo info

            $out_of_date = version_compare(
                $this->get_version_number($this->remote_data['version']),
                $this->get_version_number($checked[$this->plugin_name]), 'gt'
            ); // Check if we're out of date

            if ($out_of_date) {

                $new_files = $this->remote_data['download_url']; // Get the ZIP

                $plugin = array( // setup our plugin info
                    'url' => $this->plugin_data["PluginURI"],
                    'slug' => $this->plugin_slug,
                    'package' => $new_files,
                    'new_version' => $this->get_version_number($this->remote_data['version'])
                );

                $transient->response[$this->plugin_name] = (object)$plugin; // Return it in response
            }
        }
        return $transient; // Return filtered transient
    }

    private function get_remote_data()
    {
        if (!is_null($this->remote_data)) return;

        // trying to get from cache first
        if (false == $response = get_transient("slash_upgrade_{$this->plugin_slug}")) {
            $request_uri = sprintf('https://raw.githubusercontent.com/%s/%s/master/info.json', $this->github_username, $this->github_repo); // Build URI

            $remote_response = wp_remote_get($request_uri);

            if (!is_wp_error($remote_response) && isset($remote_response['response']['code']) && $remote_response['response']['code'] == 200 && !empty($remote_response['body'])) {
                $response = json_decode(wp_remote_retrieve_body($remote_response), true);

                set_transient("slash_upgrade_{$this->plugin_slug}", $response, 60); // 1 minute cache
            }
        }
        $this->remote_data = $response;
    }

    private function get_version_number($tag)
    {
        return strtok($tag, '-');
    }

    public function plugin_popup($result, $action, $args)
    {
        if (empty($args->slug) || $args->slug !== $this->plugin_slug) return $result;

        $this->get_remote_data();

        return (object)[
            'name' => $this->plugin_data["Name"],
            'slug' => $this->plugin_slug,
            'requires' => $this->remote_data['requires'],
            'requires_php' => $this->remote_data['requires_php'],
            'tested' => $this->remote_data['tested'],
            'version' => $this->remote_data['version'],
            'author' => $this->plugin_data["AuthorName"],
            'author_profile' => $this->plugin_data["AuthorURI"],
            'last_updated' => $this->remote_data['last_updated'],
            'homepage' => $this->plugin_data["PluginURI"],
            'short_description' => $this->plugin_data["Description"],
            'download_link' => $this->remote_data['download_url'],
            'trunk' => $this->remote_data['download_url'],
            'sections' => $this->remote_data['sections']
        ];
    }

    function after_update($upgrader_object, $options)
    {
        if ($options['action'] == 'update' && $options['type'] === 'plugin') {
            delete_transient("slash_upgrade_{$this->plugin_slug}");
        }
    }

    public function after_install($response, $hook_extra, $result)
    {
        global $wp_filesystem; // Get global FS object

        $install_directory = $this->plugin_path; // Our plugin directory
        $wp_filesystem->move($result['destination'], $install_directory); // Move files to the plugin dir
        $result['destination'] = $install_directory; // Set the destination for the rest of the stack

        if ($this->active) { // If it was active
            activate_plugin($this->plugin_name); // Reactivate
        }

        return $result;
    }
}