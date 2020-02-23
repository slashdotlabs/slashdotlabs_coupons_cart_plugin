<?php


namespace ConsoleApp\Helpers;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessHelper
{
    static function run($command, bool $from_shell = true): Process
    {
        if ($from_shell) {
            $process = Process::fromShellCommandline($command);
        } else {
            $process = new Process($command);
        }
        $process->run();
        if (!$process->isSuccessful()) throw new ProcessFailedException($process);
        return $process;
    }

}