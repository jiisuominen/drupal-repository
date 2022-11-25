<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'changelog:project-release',
)]
final class ReleaseChangelog extends ChangelogGenerator
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->validateOptions($input, ['project', 'base']);

        $settings = $this->getProjectSettings($input->getOption('project'));

        if (!$settings) {
            return Command::FAILURE;
        }

        $this->generator->updateChangelogForRelease(
            $settings['username'],
            $settings['repository'],
            $input->getOption('base'),
        );
        return Command::SUCCESS;
    }
}
