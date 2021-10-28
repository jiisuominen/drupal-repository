<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class RebuildCommand extends BaseCommand
{
    protected static $defaultName = 'app:rebuild';

    public function configure()
    {
        $this->addArgument('package',
            InputArgument::OPTIONAL,
            'The package to update. Leave empty to update all.'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this
            ->ensureInstallation($output);

        $args = ['/usr/bin/php', '-dmemory_limit=-1', 'vendor/bin/satis', 'build', 'satis.json', 'dist'];

        if ($package = $input->getArgument('package')) {
            $args[] = $package;
        }
        $process = (new Process($args))
            ->setTimeout(3600);
        return $process->run($this->outputCallback($output));
    }
}
