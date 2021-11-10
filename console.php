<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use App\Commands\RebuildPackageIndex;
use App\Commands\TriggerDispatchEvent;
use App\Settings;
use Github\Client;
use Symfony\Component\Console\Application;

$settings = new Settings();

$application = new Application();
$application->add(new RebuildPackageIndex());
$application->add(new TriggerDispatchEvent(new Client(), $settings));
$application->run();
