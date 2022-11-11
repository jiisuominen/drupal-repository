<?php

declare(strict_types=1);

namespace App\Commands;

use App\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class Base extends Command
{

    public function __construct(protected Settings $settings)
    {
        parent::__construct();
    }

    protected function configure()
    {
        parent::configure();

        $this->addOption('ensure', mode: InputOption::VALUE_NONE);
    }

    protected function outputCallback(OutputInterface $output) : callable
    {
        return function ($type, $buffer) use ($output) {
            $output->write($buffer);
        };
    }

    protected function ensureInstallation(InputInterface $input, OutputInterface $output) : void
    {
        if (!$input->getOption('ensure')) {
            return;
        }
        (new Process(['/usr/bin/make']))
            ->setTimeout(3600)
            ->run($this->outputCallback($output));
    }
}
