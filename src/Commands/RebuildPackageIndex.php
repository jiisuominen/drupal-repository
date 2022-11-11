<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:rebuild'
)]
final class RebuildPackageIndex extends Base
{
    protected function configure(): void
    {
        parent::configure();

        $this->addArgument(
            'package',
            InputArgument::OPTIONAL,
            'The package to update. Leave empty to update all.'
        );
    }

    private function repositoryToPackageName(string $repository) :? string
    {
        $data = json_decode(file_get_contents('satis.json'));

        foreach ($data->repositories as $item) {
            if (!isset($item->url)) {
                continue;
            }

            if (str_contains(strtolower($item->url), strtolower($repository))) {
                return $item->name;
            }
        }
        return null;
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this
            ->ensureInstallation($input, $output);

        $args = ['/usr/bin/php', '-dmemory_limit=-1', 'vendor/bin/satis', 'build', 'satis.json', 'dist'];

        if ($repository = $input->getArgument('package')) {
            $args[] = $this->repositoryToPackageName($repository);
        }
        $process = (new Process($args))
            ->setTimeout(3600);
        return $process->run($this->outputCallback($output));
    }
}
