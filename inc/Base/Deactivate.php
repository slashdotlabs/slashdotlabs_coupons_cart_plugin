<?php


namespace Slash\Base;


class Deactivate
{
    public static function run()
    {
        flush_rewrite_rules();
    }
}