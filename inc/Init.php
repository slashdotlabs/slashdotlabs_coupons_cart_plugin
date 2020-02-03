<?php


namespace Slash;

use Slash\Pages\Admin;
use Slash\Pages\Submissions;
use Slash\Base\Enqueue;
use Slash\Base\SettingsLink;

final class Init
{
    /**
     * Returns array of classes to register
     * @return array Full list of classes
     */
    public static function get_services()
    {
        return [

            Enqueue::class,
            Admin::class,
            SettingsLink::class,   
        ];
    }

    /**
     * Instantiates a class from the services list and calls register method
     */
    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }

    }

    /**
     * Return a new instance of the specified class
     * @param $class
     * @return object
     */
    private static function instantiate($class)
    {
        return new $class();
    }
}