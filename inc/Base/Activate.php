<?php


namespace Slash\Base;


class Activate
{
    public static function run()
    {
        flush_rewrite_rules();
    }
}