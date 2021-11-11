<?php

declare(strict_types=1);

namespace App\Commands;

use App\Settings;
use Github\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TriggerDispatchEvent extends BaseCommand
{
    protected static $defaultName = 'app:dispatch';

    public function __construct(
        private Client $client,
        private Settings $settings,
        string $name = null
    ) {
        parent::__construct($name);
    }

    public function configure()
    {
        $this->addArgument(
            'workflowId',
            InputArgument::REQUIRED,
            'The workflow to dispatch'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this
            ->ensureInstallation($output);

        $workflowId = $input->getArgument('workflowId');
        $setting    = $this->settings->getConfig(Settings::DISPATCH_TRIGGER);

        if (!isset($setting[$workflowId])) {
            throw new \InvalidArgumentException('Settings for given workflowId not found.');
        }

        $this->client->authenticate(
            $this->settings->getEnv(Settings::GITHUB_OAUTH),
            authMethod: Client::AUTH_ACCESS_TOKEN
        );

        foreach ($setting[$workflowId] as $setting) {
            [
                'username' => $username,
                'repository' => $repository,
            ] = $setting;

            $this->client->repo()->dispatch($username, $repository, 'config_change', [
                'time' => time()
            ]);
        }
        return Command::SUCCESS;
    }
}
