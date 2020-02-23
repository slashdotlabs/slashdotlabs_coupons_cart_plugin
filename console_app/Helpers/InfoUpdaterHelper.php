<?php


namespace ConsoleApp\Helpers;


use ConsoleApp\Commands\PublishCommand;
use Exception;
use Parsedown;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class InfoUpdaterHelper
{

    /** @var SymfonyStyle */
    private $io;
    private $git_helper;
    /** @var PublishCommand */
    private $command;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;

    /**
     * InfoUpdaterHelper constructor.
     * @param PublishCommand $command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(PublishCommand $command, InputInterface $input, OutputInterface $output)
    {
        $this->command = $command;
        $this->io = $command->io;
        $this->git_helper = $command->git_helper;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param string $new_version
     * @throws Exception
     */
    public function upate_plugin_info(string $new_version)
    {
        $config = console_app_config();
        $plugin_file = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . $config['plugin_file'];
        if (!file_exists($plugin_file)) throw new Exception($plugin_file . " not found ");
        $this->backup_file($plugin_file);
        $regex_pattern = "/(?<=(\*\ Version:))(\s+)(.*)/m";
        $plugin_file_contents = file_get_contents($plugin_file);
        $plugin_file_contents = preg_replace($regex_pattern, '\\2 ' . $new_version, $plugin_file_contents);
        file_put_contents($plugin_file, $plugin_file_contents);
        $this->remove_backup($plugin_file);
        $this->io->text("<info>Updated version in plugin file</info>");

        // Commit change to file
        $this->git_helper->commit_file($plugin_file, "chore: Bumped up plugin version in file");
    }

    private function backup_file($file)
    {
        $this->io->comment("Creating a backup for " . basename($file));
        $backup_file_name = $file . ".bak";
        $contents = file_get_contents($file);
        file_put_contents($backup_file_name, $contents);
    }

    private function remove_backup($file)
    {
        $this->io->comment("Deleting " . basename($file) .
            " backup file");
        $backup_file_name = $file . ".bak";
        if (file_exists($backup_file_name)) unlink($backup_file_name);
    }

    /**
     * @param mixed $release_info
     * @throws Exception
     */
    public function update_info_json($release_info)
    {
        $info_json_file = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . 'info.json';
        if (!file_exists($info_json_file)) throw new Exception($info_json_file . " not found ");
        // create .bak file before updating just in case
        $this->backup_file($info_json_file);
        $info_content = json_decode(file_get_contents($info_json_file), true);
        $info_content['download_url'] = $release_info['zipball_url'];
        $info_content['version'] = ltrim($release_info['tag_name'], 'v');
        $info_content['last_updated'] = date("Y-m-d H:i:s", strtotime($release_info['published_at']));
        $info_content['sections']['changelog'] = $this->get_changelog();
        // update file
        $this->io->text("<info>Updating info.json with new release information...</info>");
        file_put_contents($info_json_file, json_encode($info_content, JSON_UNESCAPED_SLASHES));
        $this->remove_backup($info_json_file);
        // commit changes
        $this->git_helper->commit_file($info_json_file, "chore: Updating info on latest plugin");
        // push to remote
        $this->git_helper->push_to_remote($this->git_helper->dev_branch);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function get_changelog(): string
    {
        $changelog_file = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . 'CHANGELOG.md';
        if (!file_exists($changelog_file)) throw new Exception($changelog_file . " not found ");
        $parser = new Parsedown();
        return $parser->text(file_get_contents($changelog_file));
    }

    public function partial_changelog_update(string $new_version)
    {
        /**
         * For now you are prompted to update the changelog without out any automated
         * changes.
         */
        $this->io->note("Go ahead and update the CHANGELOG.md file, I will wait for you");
        $question = new ConfirmationQuestion("Press any key to continue...", true);
        // Doesn't matter what the response is
        $this->command->question_helper->ask($this->input, $this->output, $question);
    }

}