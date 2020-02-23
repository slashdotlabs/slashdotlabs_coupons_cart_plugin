<?php

if (!function_exists('console_app_config'))
{
    /**
     * @return array
     * @throws Exception
     */
    function console_app_config(): array
    {
        $config_file = dirname(__FILE__).DIRECTORY_SEPARATOR."config.json";
        if (!file_exists($config_file)) throw new Exception($config_file." file not found ");
        $config = json_decode(file_get_contents($config_file), true);

        $required_keys = ['github_username', 'github_repo', 'remote', 'dev_branch', 'release_branch', 'plugin_file'];
        $intersect = array_intersect($required_keys, array_keys($config));
        if (count($intersect) !== count($required_keys)) throw new Exception("Ensure you config.json has all required keys ".json_encode($required_keys));
        $config['authorize_token'] = $_ENV['authorize_token'];
        return $config;
    }
}