<?php


namespace ConsoleCommands\Helpers;


class GitHelper
{
    private $repo;
    private $username;
    private $authorize_token;
    private $dev_branch;
    private $release_branch;

    public function __construct()
    {
        $config = console_app_config();
        // TODO: init config values
    }

    private function get_latest_version(): string
    {
        // TODO:
    }

    private function clean_working_tree(): bool
    {
        // TODO:
    }

    private function checkout(string $branch, bool $exists = true)
    {
        // TODO:
    }

    private function commit(string $message)
    {
        // TODO:
    }

    private function merge(string $branch, bool $rebase = false)
    {
        // TODO:
    }

    private function create_release(string $tag_name)
    {
        // TODO:
    }
}