<?php


namespace ConsoleCommands\Helpers;


use Exception;
use Symfony\Component\Console\Style\SymfonyStyle;

class ActionHelper
{
    public $latest_version;
    public $dry_run;
    public $pre_release;
    private $io;
    private $git_helper;
    private $info_update_helper;

    /**
     * ActionHelper constructor.
     * @param SymfonyStyle $io
     * @param string $pre_release
     * @param string $dry_run
     * @throws Exception
     */
    public function __construct(SymfonyStyle $io, string $pre_release, string $dry_run)
    {
        $this->io = $io;

        $this->info_update_helper = new InfoUpdaterHelper($io);

        $this->git_helper = new GitHelper($io);
        $this->latest_version = $this->git_helper->get_latest_version();

        $this->pre_release = $pre_release;
        $this->dry_run = $dry_run;
    }

    public function bump_verion(string $bump_type)
    {
        $this->io->title("Generating next {$bump_type} release");

        // remove pre-release or build metadata
        $latest = strtok($this->latest_version, '-');
        list($major, $minor, $patch) = explode('.', $latest);

        switch ($bump_type) {
            case 'major':
                $major = (int)$major + 1;
                $minor = 0;
                $patch = 0;
                break;
            case 'minor':
                $minor = (int)$minor + 1;
                $patch = 0;
                break;
            case 'patch':
                $patch = (int)$patch + 1;
                break;
            default:
                break;
        }

        $new_version = implode('.', [$major, $minor, $patch]);
        return $this->pre_release ? $new_version . '-' . $this->pre_release : $new_version;
    }

    /**
     * @param $new_version
     * @throws Exception
     */
    public function publish_release($new_version)
    {
        if (!$this->dry_run) {
            $this->info_update_helper->upate_plugin_info($new_version);
            $this->info_update_helper->partial_changelog_update($new_version);

            // checkout to release branch
            $this->git_helper->checkout_to_release();

            // create tag
            $new_tag = $this->git_helper->create_tag($new_version);
            // push to remote
            $this->git_helper->push_to_remote($new_tag);
            // create release
            $release_info = $this->git_helper->create_release($new_tag, $this->pre_release);
            // checkout to dev branch
            $this->git_helper->checkout($this->git_helper->dev_branch);
            // delete release branch
            $this->git_helper->delete_branch($this->git_helper->release_branch);
            // update info.json
            $this->info_update_helper->update_info_json($release_info);
        }
        $this->io->text("<info>New version:</info> $new_version");
    }
}