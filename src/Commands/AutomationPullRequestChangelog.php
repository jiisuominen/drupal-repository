<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'changelog:automation-pull-request',
)]
final class AutomationPullRequestChangelog extends ChangelogGenerator
{
    protected function configure(): void
    {
        parent::configure();
        $this->addOption('number', mode: InputOption::VALUE_REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $settings = $this->getProjectSettings($input->getOption('project'));

        if (!$settings) {
            return Command::FAILURE;
        }

        $this->generator->updateChangelogForPullRequest(
            $settings['username'],
            $settings['repository'],
            $input->getOption('base'),
            $input->getOption('head'),
            $input->getOption('number')
        );
        return Command::SUCCESS;
    }

}
