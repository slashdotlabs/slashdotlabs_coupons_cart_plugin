<?php


namespace ConsoleApp\Commands;


use ConsoleApp\Helpers\ProcessHelper;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteCommand extends Command
{
    protected static string $defaultName = "release:delete";

    public $repo;
    public $username;
    public $authorize_token;
    public $remote;
    public $dev_branch;
    public $release_branch;

    private Client $client;
    private SymfonyStyle $io;

    /**
     * DeleteCommand constructor.
     * @param string|null $name
     * @throws Exception
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        try {
            $this->delete_tags();
            foreach ($this->get_all_draft_releases() as $release) {
                $this->delete_release($release);
            }
            $this->io->success("Done");
        } catch (Exception $e) {
            $this->io->text($e->getMessage());
            $this->io->error("Execution failed");
        }
        return 0;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function get_all_draft_releases()
    {
        $this->io->text("<info>Fetching all releases ...</info>");
        $res = $this->client->get("/repos/$this->username/$this->repo/releases", [
            'headers' => [
                'Accept' => 'application/vnd.github.v3raw+json',
                'Authorization' => "token $this->authorize_token",
            ]
        ]);
        if ($res->getStatusCode() !== 200) throw new Exception("Could not reach remote");
        $all_releases = json_decode($res->getBody()->getContents(), true);
        return array_filter($all_releases, fn($release) => $release['draft']);
    }

    /**
     * @param $release
     * @throws Exception
     */
    private function delete_release($release)
    {
        $this->io->text(sprintf("<info>Deleting release %s ...</info>", $release['name']));
        $res = $this->client->delete("/repos/$this->username/$this->repo/releases/" . $release['id'], [
            'headers' => [
                'Accept' => 'application/vnd.github.v3raw+json',
                'Authorization' => "token $this->authorize_token",
            ]
        ]);
        if ($res->getStatusCode() !== 204) throw new Exception("Could not reach remote \n" . $res->getBody()->getContents());
        $this->io->text("Deleted Release " . $release['name']);
    }

    private function delete_tags()
    {
        $tags = [
            'v1.2.1',
        ];
        foreach ($tags as $tag) {
            $command = "git tag -d $tag";
            $this->io->comment($command);
            $process = ProcessHelper::run($command);
            $this->io->text($process->getOutput());

            $command = "git push origin --delete $tag";
            $this->io->comment($command);
            $process = ProcessHelper::run($command);
            $this->io->text($process->getOutput());
        }
    }
}