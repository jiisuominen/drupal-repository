<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Utils;

final class Settings
{
    public const DISPATCH_SETTINGS = 'DISPATCH_SETTINGS';
    public const GITHUB_OAUTH = 'GITHUB_OAUTH';
    public const WEBHOOK_UPDATE_SECRET = 'WEBHOOK_UPDATE_SECRET';
    public const WEBHOOK_SECRET = 'WEBHOOK_SECRET';

    protected function assertSetting(string $name) : void
    {
        if (!defined("self::$name")) {
            throw new \RuntimeException(sprintf('"%s" setting not found.', $name));
        }

        if (!getenv($name)) {
            throw new \RuntimeException('"%s" setting is not set.', $name);
        }
    }

    public function getSetting(string $name) : string
    {
        $this->assertSetting($name);
        return getenv($name);
    }

    public function getJsonSetting(string $name) : array
    {
        $this->assertSetting($name);
        return Utils::jsonDecode(getenv($name), true);
    }
}
