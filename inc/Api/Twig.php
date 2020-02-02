<?php


namespace Slash\Api;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class Twig
{
    protected static $instance = null;

    protected function __construct()
    {
        // Empty on purpose
    }

    protected function __clone()
    {
        // Empty on purpose
    }

    public static function instance()
    {
        if (self::$instance === null) {
            $root = plugin_dir_path(dirname(__FILE__, 2));
            $loader = new FilesystemLoader($root . '/templates');
            self::$instance = new Environment($loader, ['cache', $root.'/storage/cache']);
        }
        return self::$instance;
    }
}