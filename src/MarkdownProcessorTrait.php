<?php

declare(strict_types = 1);

namespace App;

trait MarkdownProcessorTrait
{
    public function processMarkdown(string $note) : string
    {
        // Remove usernames from notes. For example "Update mariadb Docker tag to v10.10 by @renovate in {url}"
        // should become "Update mariadb Docker in {url}".
        $note = preg_replace(
            '/\b by @\b[\w-]*/',
            '',
            $note,
        );

        // Remove New Contributors section This should remove everything between:
        // '### New Contributors' and the next line starting with either '##' or '**'.
        $note = preg_replace('/### New Contributors[\s\S]+?(\*\*|##)/', '${1}', $note);

        // Convert issue IDs to Jira links.
        return preg_replace(
            '/\b[UHF][A-Z0-9_]+-[1-9][0-9]*/',
            sprintf('[${0}](%s/${0})', self::JIRA_BASE_URL),
            $note
        );
    }
}
