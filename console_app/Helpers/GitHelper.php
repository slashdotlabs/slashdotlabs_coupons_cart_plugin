<?php


namespace ConsoleCommands\Helpers;


use ConsoleCommands\PublishCommand;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GitHelper
{
    public $repo;
    public $username;
    public $authorize_token;
    public $remote;
    public $dev_branch;
    public $release_branch;

    /** @var SymfonyStyle */
    private $io;
    private $command;

    /**
     * GitHelper constructor.
     * @param PublishCommand $command
     * @throws Exception
     */
    public function __construct(PublishCommand $command)
    {
        $this->command = $command;
        $this->io = $command->io;

        $config = console_app_config();
        $this->repo = $config['github_repo'];
        $this->username = $config['github_username'];
        $this->authorize_token = $config['authorize_token'];
        $this->remote = $config['remote'];
        $this->dev_branch = $config['dev_branch'];
        $this->release_branch = $config['release_branch'];
    }

    public function get_latest_version(): string
    {
        $this->fetch_remote();

        // Get latest tag
        $process = ProcessHelper::run("git describe --abbrev=0 --tags");
        $tag = $process->getOutput();
        return !empty($tag) || $tag !== "" ? ltrim($tag, 'v') : "No versions created";
    }

    public function fetch_remote()
    {
        $this->io->text("<info>Updating from origin...</info>");
        $process = ProcessHelper::run("git fetch {$this->remote}");
    }

    public function is_clean_working_tree(): bool
    {
        $process = ProcessHelper::run('[[ -n $(git status) ]] || echo clean');
        return trim($process->getOutput()) === "clean";
    }

    public function checkout(string $branch, bool $exists = true)
    {
        $command = $exists ? "git checkout {$branch}" : "git checkout -b {$branch}";
        $this->io->comment($command);
        $process = ProcessHelper::run($command);
        $this->io->text($process->getOutput());
    }

    public function delete_branch(string $branch)
    {
        if ($branch === $this->dev_branch) {
            $this->io->caution("Sorry, can't delete dev_branch {$this->dev_branch}");
            return;
        }
        $command = "git branch -D {$branch}";
        $this->io->comment($command);
        $process = ProcessHelper::run($command);
        $this->io->text($process->getOutput());
    }

    public function commit_file(string $file_path, string $message)
    {
        $command = "git add $file_path && git commit -m '$message'";
        $this->io->comment($command);
        $process = ProcessHelper::run($command);
        $this->io->text($process->getOutput());
    }

    public function commit_changes(string $message)
    {
        $command = "git commit -am '$message'";
        $this->io->comment($command);
        $process = ProcessHelper::run($command);
        $this->io->text($process->getOutput());
    }

    public function rebase(string $branch)
    {
        // stash -> rebase -> pop
        $stash_command = "git stash save 'Uncommitted changes before rebase'";
        $this->io->comment($stash_command);
        $process = ProcessHelper::run($stash_command);
        $this->io->text($process->getOutput());

        $rebase_command = "git rebase $branch";
        $this->io->comment($rebase_command);
        $process = ProcessHelper::run($rebase_command);
        $this->io->text($process->getOutput());

        $pop_stash_command = "git stash pop";
        $this->io->comment($pop_stash_command);
        $process = ProcessHelper::run($pop_stash_command);
        $this->io->text($process->getOutput());
    }

    public function create_tag(string $new_version)
    {
        $this->io->text("<info>Creating new tag...</info>");
        $command = "git tag v{$new_version}";
        $this->io->comment($command);
        $process = ProcessHelper::run($command);
        $this->io->text($process->getOutput());
        $this->io->text("Created tag: v{$new_version}");
        return "v$new_version";
    }

    public function push_to_remote(string $branch)
    {
        $this->io->text("<info>Pushing changes upstream...</info>");
        $command = "git push {$this->remote} {$branch}";
        $process = ProcessHelper::run($command);
        $this->io->text($process->getOutput());
        $this->io->text("<info>Changes pushed to remote</info>");
    }

    /**
     * @param string $tag_name
     * @param bool $pre_release
     * @return StreamInterface
     * @throws Exception
     */
    public function create_release(string $tag_name, bool $pre_release = false)
    {
        $endpoint = "/repos/$this->username/$this->username/releases";
        $headers = [
            'Accept' => 'application/vnd.github.v3raw+json',
            'Authorization' => 'token ' . $this->authorize_token,
        ];
        $client = new Client(['base_uri' => "https://api.github.com"]);
        $res = $client->post($endpoint, [
            'headers' => $headers,
            'body' => [
                "tag_name" => $tag_name,
                "name" => $tag_name,
                "body" => "Release $tag_name",
                "draft" => false,
                "prerelease" => $pre_release
            ]
        ]);
        if ($res->getStatusCode() !== 200) throw new Exception("Could not reach remote to create release");
        return $res->getBody();
    }
}