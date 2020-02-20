<?php

namespace ConsoleCommands;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PublishReleaseCommand extends Command
{
    protected static $defaultName = 'app:release';
    private $releases;
    private $github_username;
    private $github_repo;
    private $authorize_token; // only for private repos

    protected function configure()
    {
        $this->setDescription("Creates a new release on Github repo");
        $this->setHelp("This helps you create a new release on the configured Github repo");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        try {
            // eg release --fix|minor|major --pre-release --b
            $this->get_config();

            $io->writeln($this->github_username);

        } catch (Exception $e) {
            $io->error($e->getMessage());
        }
        return 0;
    }

    /**
     * @throws Exception
     */
    private function get_config()
    {
        $config_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.json';
        if (!file_exists($config_file)) return;
        $config = json_decode(file_get_contents($config_file), true);

        if (!$this->has_required_config_keys($config)) throw new Exception("Ensure you have a valid config.json with all required keys");

        $this->github_username = $config['github_username'];
        $this->github_repo = $config['github_repo'];
        $this->authorize_token = $config['authorize_token'];
    }

    private function has_required_config_keys($config)
    {
        $required_keys = ['github_username', 'github_repo', 'authorize_token'];
        $intersect = array_intersect($required_keys, array_keys($config));
        return count($intersect) === count($required_keys);
    }
}
