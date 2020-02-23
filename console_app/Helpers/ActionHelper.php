<?php


namespace ConsoleApp\Helpers;


use ConsoleApp\Commands\PublishCommand;
use Exception;

class ActionHelper
{
    private $command;

    public function __construct(PublishCommand $command)
    {
        $this->command = $command;
    }

    public function get_latest_version()
    {
        // Gets the latest version tag || empty if none
        $this->command->io->title("Getting you the latest version");
        $latest_version = $this->command->git_helper->get_latest_version();
        $this->command->io->text([
            "<info>Latest Version:</info> {$latest_version}"
        ]);
    }

    /**
     * @param string $action
     * @throws Exception
     */
    public function publish_release(string $action)
    {
        $new_version = $this->bump_verion($action);
        if (!$this->command->dry_run) {
            $this->command->info_update_helper->upate_plugin_info($new_version);
            $this->command->info_update_helper->partial_changelog_update($new_version);

            // checkout to release branch
            $this->command->git_helper->checkout($this->command->git_helper->release_branch, false);

            // create tag
            $new_tag = $this->command->git_helper->create_tag($new_version);
            // push to remote
            $this->command->git_helper->push_to_remote($new_tag);
            // create release
            $pre_release = !empty($this->command->pre_release) && !is_null($this->command->pre_release);
            $release_info = $this->command->git_helper->create_release($new_tag, $pre_release);
            // checkout to dev branch
            $this->command->git_helper->checkout($this->command->git_helper->dev_branch);
            // delete release branch
            $this->command->git_helper->delete_branch($this->command->git_helper->release_branch);
            // update info.json
            $this->command->info_update_helper->update_info_json($release_info);
        }
        $this->command->io->text("<info>New version:</info> $new_version");
    }

    private function bump_verion(string $bump_type)
    {
        $this->command->io->title("Generating next {$bump_type} release");

        // remove pre-release or build metadata
        $latest = strtok($this->command->git_helper->get_latest_version(), '-');
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
        return $this->command->pre_release ? $new_version . '-' . $this->command->pre_release : $new_version;
    }
}