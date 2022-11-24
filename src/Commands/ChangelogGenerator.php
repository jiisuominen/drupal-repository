<?php

declare(strict_types = 1);

namespace App\Commands;

use App\ReleaseNoteGenerator;
use App\Settings;
use Symfony\Component\Console\Input\InputOption;

abstract class ChangelogGenerator extends Base
{

    public function __construct(
        protected ReleaseNoteGenerator $generator,
        Settings $settings,
    ) {
        parent::__construct($settings);
    }

    protected function getProjectSettings(string $projectName) : ? array
    {
        foreach ($this->settings->get(Settings::CHANGELOG_PROJECTS) as $project) {
            ['username' => $username, 'repository' => $repository] = $project;
            $name = strtolower(sprintf('%s/%s', $username, $repository));

            if (strtolower($projectName) === $name) {
                return $project;
            }
        }
        return null;
    }

    protected function configure(): void
    {
        $this->addOption('project', mode: InputOption::VALUE_REQUIRED)
            ->addOption('base', mode: InputOption::VALUE_REQUIRED)
            ->addOption('head', mode: InputOption::VALUE_OPTIONAL);
    }
}
