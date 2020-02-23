<?php


namespace ConsoleApp\Helpers;


use ConsoleApp\Commands\PublishCommand;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Symfony\Component\Console\Style\SymfonyStyle;

class GitHelper
{
    public $repo;
    public $username;
    public $authorize_token;
    public $remote;
    public $dev_branch;
    public $release_branch;
    private $command;

    /** @var SymfonyStyle */
    private $io;
    /** @var Client */
    private $client;

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

        $stack = HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory());

        $this->client = new Client(['handler' => $stack, 'base_uri' => "https://api.github.com"]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function get_latest_version(): string
    {
        $this->io->text("<info>Fetching all releases ...</info>");
        $res = $this->client->get("/repos/$this->username/$this->repo/releases", [
            'headers' => [
                'Accept' => 'application/vnd.github.v3raw+json',
            ]
        ]);
        if ($res->getStatusCode() !== 200) throw new Exception("Could not reach remote to create release");
        $releases = json_decode($res->getBody()->getContents(), true);
        if (count($releases) === 0 || empty($releases)) return "No versions created";
        return ltrim($releases[0]['tag_name'], 'v');
    }

    public function fetch_remote()
    {
        $this->io->text("<info>Updating from origin...</info>");
        $process = ProcessHelper::run("git fetch {$this->remote}");
    }

    public function is_clean_working_tree(): bool
    {
        $process = ProcessHelper::run('[[ -n $(git status --porcelain) ]] || echo clean');
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
     * @return array
     * @throws Exception
     */
    public function create_release(string $tag_name, bool $pre_release = false)
    {
        $this->io->text("<info>Creating release ...</info>");
        $res = $this->client->post("/repos/$this->username/$this->repo/releases", [
            'headers' => [
                'Accept' => 'application/vnd.github.v3raw+json',
                'Authorization' => "token $this->authorize_token",
            ],
            'json' => [
                "tag_name" => $tag_name,
                "name" => $tag_name,
                "body" => "Release $tag_name",
                "draft" => false,
                "prerelease" => $pre_release
            ]
        ]);
        if ($res->getStatusCode() !== 201) throw new Exception("Could not reach remote to create release");
        return json_decode($res->getBody()->getContents(), true);
    }
}