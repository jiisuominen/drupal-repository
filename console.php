<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use App\Commands\RebuildCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new RebuildCommand());
$application->run();
