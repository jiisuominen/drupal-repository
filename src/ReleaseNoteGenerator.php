<?php

declare(strict_types = 1);

namespace App;

use ComposerLockParser\Package;
use Github\AuthMethod;
use Github\Client;
use Github\Exception\ExceptionInterface;

final class ReleaseNoteGenerator
{
    private const JIRA_BASE_URL = 'https://helsinkisolutionoffice.atlassian.net/browse';

    public function __construct(
        private Client $client,
        private string $authToken,
        private array $allowedPackages
    ) {
    }

    private function createNote(string $username, string $repository, array $version): ? string
    {
        $this
            ->client
            ->authenticate($this->authToken, authMethod: AuthMethod::ACCESS_TOKEN);

        try {
            $note = $this
                ->client
                ->repos()
                ->releases()
                ->generateNotes($username, $repository, [
                    'previous_tag_name' => $version['base'],
                    'target_commitish' => $version['head'],
                    'tag_name' => $version['head'],
                ]);
        } catch (ExceptionInterface) {
            return null;
        }

        return "## [$username/$repository](https://github.com/$username/$repository): " .
            "{$version['base']} to {$version['head']}\n" .
            // Convert previous h2 to h3.
            str_replace('##', '###', $note['body']) .
            "\n";
    }

    private function postProcessNote(string $note) : string
    {
        // Convert issue IDs to Jira links.
        return preg_replace(
            '/\b[UHF][A-Z0-9_]+-[1-9][0-9]*/',
            sprintf('[${0}](%s/${0})', self::JIRA_BASE_URL),
            $note
        );
    }

    private function hasChanges(string $username, string $repository, string $base, string $head): bool
    {
        try {
            $compare = $this->client
                ->repos()
                ->commits()
                ->compare($username, $repository, $base, $head);
        } catch (ExceptionInterface) {
            return false;
        }

        $hasComposerChanges = array_filter($compare['files'], function (array $file) {
            return $file['filename'] === 'composer.lock';
        });
        return count($hasComposerChanges) > 0;
    }

    private function getComposerPackageVersions(string $username, $repository, string $reference): array
    {
        try {
            $data = $this->client
                ->repos()
                ->contents()
                ->rawDownload($username, $repository, 'composer.lock', $reference);
        } catch (ExceptionInterface) {
            return [];
        }
        $decoded = json_decode($data, true);

        $packages = [];
        foreach ($decoded['packages'] as $packageInfo) {
            $package = Package::factory($packageInfo);

            // Ignore non-whitelisted packages.
            if (!in_array($package->getName(), array_keys($this->allowedPackages))) {
                continue;
            }
            $packages[$package->getName()] = $package->getVersion();
        }
        return $packages;
    }

    private function getUpdatedDependencies(string $username, string $repository, string $base, string $head): array
    {
        $previousVersions = $this->getComposerPackageVersions($username, $repository, $base);
        $latestVersions = $this->getComposerPackageVersions($username, $repository, $head);

        $diff = array_keys(array_diff($previousVersions, $latestVersions));

        $versions = [];
        foreach ($diff as $name) {
            $versions[$name] = [
                'base' => $previousVersions[$name],
                'head' => $latestVersions[$name],
            ];
        }
        return $versions;
    }

    public function createChangeLog(
        string $username,
        string $repository,
        string $base
    ): void {
        $releases = $this->client->repos()->releases()->all($username, $repository);

        // First item should always be the first release and the next item
        // is the previous.
        $latest = current($releases);
        $previous = next($releases);

        if (!$latest || !$previous) {
            throw new \InvalidArgumentException('Failed to parse latest or previous release.');
        }
        if (!$this->hasChanges($username, $repository, $previous['tag_name'], $latest['tag_name'])) {
            return;
        }

        // Create changelog for project repository.
        $changelog = $this->createNote($username, $repository, [
            'base' => $previous['tag_name'],
            'head' => $latest['tag_name']
        ]);
        $changelog .= "\n";

        // Create changelog for each updated dependency.
        $versions = $this->getUpdatedDependencies($username, $repository, $previous['tag_name'], $latest['tag_name']);

        foreach ($versions as $name => $version) {
            $package = $this->allowedPackages[$name];

            $changelog .= $this->createNote(
                $package->extra->username,
                $package->extra->repository,
                $version
            );
        }

        $changelog = $this->postProcessNote($changelog);

        if (mb_strlen($changelog) < 1) {
            return;
        }
        $this->client
            ->authenticate($this->authToken, authMethod: AuthMethod::ACCESS_TOKEN);

        try {
            $this->client
                ->repos()
                ->releases()
                ->edit($username, $repository, $latest['id'], [
                    'body' => $changelog,
                ]);
        } catch (ExceptionInterface) {
        }
    }
}
