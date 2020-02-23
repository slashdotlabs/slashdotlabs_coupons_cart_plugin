<?php

namespace ConsoleApp\Commands;

use ConsoleApp\Helpers\ActionHelper;
use ConsoleApp\Helpers\GitHelper;
use ConsoleApp\Helpers\InfoUpdaterHelper;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PublishCommand extends Command
{
    protected static string $defaultName = "publish";
    public $dry_run;
    public $pre_release;

    public SymfonyStyle $io;
    public GitHelper $git_helper;
    public ActionHelper $action_helper;
    public InfoUpdaterHelper $info_update_helper;
    public QuestionHelper $question_helper;


    protected function configure()
    {
        $this->setDescription("Create versions based on Semver guidlines");

        $this->register_options();
        $this->register_arguments();
    }

    private function register_options()
    {
        // Dry run
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Get the the next version without persisting changes');

        // Pre-release
        $this->addOption('pre-release', null, InputOption::VALUE_REQUIRED, 'Create a pre-release version');

        // Build
        $this->addOption('build', 'b', InputOption::VALUE_OPTIONAL, 'Add build metadata');
    }

    private function register_arguments()
    {
        // Action to run (get | help | major | minor | patch)
        $this->addArgument('action', InputArgument::REQUIRED, 'Action you want to run');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init all tools
        $this->io = new SymfonyStyle($input, $output);
        $this->question_helper = $this->getHelper('question');
        $this->git_helper = new GitHelper($this);
        $this->action_helper = new ActionHelper($this);
        $this->info_update_helper = new InfoUpdaterHelper($this, $input, $output);

        try {
            if(!$this->git_helper->is_clean_working_tree()) throw new Exception("Ensure you have a clean working tree first.");

            $this->handle_options($input);

            $action = $input->getArgument('action');
            $valid_actions = ['get', 'major', 'minor', 'patch'];
            if (!in_array($action, $valid_actions)) throw new Exception('Enter a valid action. Run help for more information');
            if ($action === "get") {
                $this->action_helper->get_latest_version();
            } else {
                $this->action_helper->publish_release($action);
            }
            $this->io->success("Done");
        } catch (Exception $e) {
            $this->io->text($e->getMessage());
            $this->io->error("Execution failed");
        }
        return 0;
    }

    private function handle_options(InputInterface $input)
    {
        $this->dry_run = $input->getOption('dry-run');
        if ($this->dry_run) $this->io->note("In dry run mode, changes won't persist");

        // Handle pre-release
        $this->pre_release = $input->getOption('pre-release');
        if ($this->pre_release) $this->io->note("This is a {$this->pre_release} pre release");
    }
}
