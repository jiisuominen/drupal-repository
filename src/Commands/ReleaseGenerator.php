<?php

declare(strict_types=1);

namespace App\Commands;

use App\ReleaseNoteGenerator;
use App\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ReleaseGenerator extends BaseCommand
{
    protected static $defaultName = 'app:release-generator';

    public function __construct(
        private ReleaseNoteGenerator $generator,
        Settings $settings,
        string $name = null
    ) {
        parent::__construct($settings, $name);
    }

    public function configure()
    {
        $this->addArgument(
            'project',
            InputArgument::REQUIRED,
            'The project name. See console.php for available project names.'
        );
        $this->addArgument(
            'base',
            InputArgument::REQUIRED,
            'The base commit.'
        );
    }

    private function getProjectSettings(string $projectName) : ? array
    {
        foreach ($this->settings->get(Settings::ALLOWED_PROJECTS) as $project) {
            ['username' => $username, 'repository' => $repository] = $project;
            $name = strtolower(sprintf('%s/%s', $username, $repository));

            if (strtolower($projectName) === $name) {
                return $project;
            }
        }
        return null;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$settings = $this->getProjectSettings($input->getArgument('project'))) {
            $output->writeln('<error>Failed to map project</error>');
            return Command::FAILURE;
        }
        $this->generator->createChangeLog(
            $settings['username'],
            $settings['repository'],
            $input->getArgument('base'),
        );

        return Command::SUCCESS;
    }

}
