<?php


namespace Slash\Base;

use Slash\Database\Migrations;

class Deactivate
{
    public static function run()
    {
        flush_rewrite_rules();
        Migrations::dropPaymentsTable();
    }
}