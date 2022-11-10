<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use App\Commands\RebuildPackageIndex;
use App\Commands\ReleaseGenerator;
use App\Commands\TriggerDispatchEvent;
use App\ReleaseNoteGenerator;
use App\Settings;
use Github\Client;
use Symfony\Component\Console\Application;

$projects = [
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
    [
        'username' => 'City-of-Helsinki',
        'repository' => 'drupal-helfi-rekry',
    ],
];

$data = json_decode(file_get_contents('satis.json'));

$packages = [];
foreach ($data->repositories as $item) {
    $packages[$item->name] = $item;
}

$config = [
    Settings::ENV => getenv('APP_ENV') ?: 'local',
    Settings::GITHUB_OAUTH => getenv(Settings::GITHUB_OAUTH),
    Settings::ALLOWED_PROJECTS => $projects,
    Settings::ALLOWED_PACKAGES => array_filter($packages, function (object $package) : bool {
        $isWhitelisted = !empty($package->extra->whitelisted);

        if ($isWhitelisted && !isset($package->extra->username, $package->extra->repository)) {
            throw new \InvalidArgumentException(
                sprintf('Missing required "repository" or "username" for %s', $package->name)
            );
        }
        return $isWhitelisted;
    }),
    Settings::DISPATCH_TRIGGER => [
        'config-update' => $projects,
    ],
];

$settings = new Settings($config);
$client = new Client();

$application = new Application();
$application->add(new RebuildPackageIndex($settings));
$application->add(
    new ReleaseGenerator(
        new ReleaseNoteGenerator(
            $client,
            $settings->get(Settings::GITHUB_OAUTH),
            $settings->get(Settings::ALLOWED_PACKAGES)
        ),
        $settings
    )
);
$application->add(new TriggerDispatchEvent(new Client(), $settings));
$application->run();
