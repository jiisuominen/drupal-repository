<?php

declare(strict_types=1);

namespace App\Commands;

use App\Settings;
use Github\AuthMethod;
use Github\Client;
use Github\Exception\ExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:dispatch',
)]
final class TriggerDispatchEvent extends Base
{
    public function __construct(
        private Client $client,
        Settings $settings,
    ) {
        parent::__construct($settings);
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
        $setting    = $this->settings->get(Settings::DISPATCH_TRIGGER);

        if (!isset($setting[$workflowId])) {
            throw new \InvalidArgumentException('Settings for given workflowId not found.');
        }

        $this->client->authenticate(
            $this->settings->get(Settings::GITHUB_OAUTH),
            authMethod: AuthMethod::ACCESS_TOKEN
        );

        $exception = null;
        foreach ($setting[$workflowId] as $setting) {
            [
                'username' => $username,
                'repository' => $repository,
            ] = $setting;

            try {
                $this->client->repo()->dispatch($username, $repository, 'config_change', [
                  'time' => time()
                ]);
            } catch (ExceptionInterface $exception) {
                $output->writeln(
                    vsprintf('[Github error] Dispatch failed for: %s/%s. See %s for more information.', [
                      $username,
                      $repository,
                      'https://github.com/City-of-Helsinki/drupal-helfi-platform/blob/main/documentation/automatic-updates.md#automatically-trigger-config-update-on-all-whitelisted-projects'
                    ])
                );
            } catch (\Exception $exception) {
                $output->writeln(
                    sprintf('[General error] Dispatch failed for: %s/%s', $username, $repository)
                );
            }
        }

        // Allow individual repositories to fail, but catch the latest exception and re-throw it.
        if ($exception) {
            throw $exception;
        }
        return Command::SUCCESS;
    }
}
