<?php

declare(strict_types = 1);

namespace App;

use App\Commands\AutomationPullRequestChangelog;
use App\Commands\RebuildPackageIndex;
use App\Commands\ReleaseChangelog;
use App\Commands\TriggerDispatchEvent;
use Github\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Application;

class Kernel
{
    private static ?Application $application = null;

    public static function boot(Settings $settings): Application
    {
        if (!static::$application) {
            $client = new Client();
            $releaseNoteGenerator = new ReleaseNoteGenerator(
                $client,
                new FilesystemAdapter(
                    defaultLifetime: 60,
                ),
                $settings->get(Settings::GITHUB_OAUTH),
                $settings->get(Settings::CHANGELOG_ALLOWED_PACKAGES)
            );

            $application = new Application();
            $application->add(new RebuildPackageIndex($settings));
            $application->add(new ReleaseChangelog($releaseNoteGenerator, $settings));
            $application->add(new AutomationPullRequestChangelog($releaseNoteGenerator, $settings));
            $application->add(new TriggerDispatchEvent(new Client(), $settings));
            static::$application = $application;
        }

        return static::$application;
    }
}
