<?php

if (!function_exists('console_app_config'))
{
    function console_app_config()
    {
        $config_file = dirname(__FILE__).DIRECTORY_SEPARATOR."config.json";
        if (!file_exists($config_file)) return false;
        $config = json_decode(file_get_contents($config_file));
        $required_keys = [];
        // TODO: validate it has all required keys

        return $config;
    }
}