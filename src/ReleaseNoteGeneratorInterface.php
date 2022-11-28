<?php

namespace App;

interface ReleaseNoteGeneratorInterface
{
    public function updateChangelogForRelease(
        string $username,
        string $repository,
        string $base,
    ): void;

    public function updateChangelogForPullRequest(
        string $username,
        string $repository,
        string $base,
        string $head,
        string $pullRequest
    ): void;
}
