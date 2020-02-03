<?php


namespace Slash\Base;

use Slash\Database\Migrations;

class Activate
{
    public static function run()
    {
        flush_rewrite_rules();
        Migrations::createPaymentsTable();
    }

}