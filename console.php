<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use App\Commands\AutomationPullRequestChangelog;
use App\Commands\RebuildPackageIndex;
use App\Commands\ReleaseChangelog;
use App\Commands\TriggerDispatchEvent;
use App\ReleaseNoteGenerator;
use App\Settings;
use Github\Client;
use Symfony\Component\Console\Application;

$projects = [
    [
        'username' => 'city-of-helsinki',
        'repository' => 'drupal-helfi-kymp',
    ],
    [
        'username' => 'city-of-helsinki',
        'repository' => 'drupal-helfi-sote',
    ],
    [
        'username' => 'city-of-helsinki',
        'repository' => 'drupal-helfi-strategia',
    ],
    [
        'username' => 'city-of-helsinki',
        'repository' => 'drupal-helfi-tyo-yrittaminen',
    ],
    [
        'username' => 'city-of-helsinki',
        'repository' => 'drupal-helfi-kasvatus-koulutus',
    ],
    [
        'username' => 'city-of-helsinki',
        'repository' => 'drupal-helfi-asuminen',
    ],
    [
        'username' => 'city-of-helsinki',
        'repository' => 'drupal-helfi-etusivu',
    ],
    [
        'username' => 'city-of-helsinki',
        'repository' => 'drupal-helfi-kuva',
    ],
    [
        'username' => 'city-of-helsinki',
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
$releaseNoteGenerator = new ReleaseNoteGenerator(
    $client,
    $settings->get(Settings::GITHUB_OAUTH),
    $settings->get(Settings::ALLOWED_PACKAGES)
);

$application = new Application();
$application->add(new RebuildPackageIndex($settings));
$application->add(new ReleaseChangelog($releaseNoteGenerator, $settings));
$application->add(new AutomationPullRequestChangelog($releaseNoteGenerator, $settings));
$application->add(new TriggerDispatchEvent(new Client(), $settings));
$application->run();
