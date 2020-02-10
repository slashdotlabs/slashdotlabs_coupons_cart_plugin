<?php


namespace Slash\Api;


use Slash\Base\BaseController;

class Logger extends BaseController
{
    public $logDir;

    function __construct()
    {
        parent::__construct();

        $this->logDir = $this->plugin_path."storage/logs/";

        if (!file_exists($this->logDir))
        {
            mkdir($this->logDir, 0755, true);
        }
    }

    public function log(string $content)
    {
        $filename = date("Ymd").".log";
        $destination = $this->logDir.$filename;
        $now = date("Y-m-d H:i:s");
        $content = "[$now] ".$content.PHP_EOL;
        error_log($content, 3, $destination);
    }

}