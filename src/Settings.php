<?php

declare(strict_types=1);

namespace App;

final class Settings
{
    public const DISPATCH_TRIGGER = 'dispatch-triggers';
    public const GITHUB_OAUTH = 'GITHUB_OAUTH';
    public const WEBHOOK_UPDATE_SECRET = 'WEBHOOK_UPDATE_SECRET';
    public const WEBHOOK_SECRET = 'WEBHOOK_SECRET';

    public function __construct(private array $config)
    {
    }

    public function getEnv(string $name) : string
    {
        if (!$value = getenv($name)) {
            throw new \RuntimeException('"%s" setting is not set.', $name);
        }
        return $value;
    }

    public function getConfig(string $name) : array|string
    {
        if (!isset($this->config[$name])) {
            throw new \InvalidArgumentException("Configuration $name does not exist.");
        }
        return $this->config[$name];
    }
}
