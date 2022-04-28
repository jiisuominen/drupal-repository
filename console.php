<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use App\Commands\RebuildPackageIndex;
use App\Commands\TriggerDispatchEvent;
use App\Settings;
use Github\Client;
use Symfony\Component\Console\Application;

$config = [
    'dispatch-triggers' => [
        'config-update' => [
            [
                'username' => 'City-of-Helsinki',
                'repository' => 'drupal-helfi-kymp',
            ],
            [
                'username' => 'City-of-Helsinki',
                'repository' => 'drupal-helfi-sote',
            ],
            [
                'username' => 'City-of-Helsinki',
                'repository' => 'drupal-helfi-strategia',
            ],
            [
                'username' => 'City-of-Helsinki',
                'repository' => 'drupal-helfi-tyo-yrittaminen',
            ],
            [
                'username' => 'City-of-Helsinki',
                'repository' => 'drupal-helfi-kasvatus-koulutus',
            ],
            [
                'username' => 'City-of-Helsinki',
                'repository' => 'drupal-helfi-asuminen',
            ],
            [
                'username' => 'City-of-Helsinki',
                'repository' => 'drupal-helfi-etusivu',
            ],
	    [
                'username' => 'City-of-Helsinki',
		'repository' => 'drupal-helfi-kuva',
            ],
        ],
    ],
];

$settings = new Settings($config);

$application = new Application();
$application->add(new RebuildPackageIndex());
$application->add(new TriggerDispatchEvent(new Client(), $settings));
$application->run();
