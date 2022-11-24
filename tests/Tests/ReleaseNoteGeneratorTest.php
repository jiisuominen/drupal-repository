<?php

namespace App\Tests;

use App\ReleaseNoteGenerator;
use Github\Client;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \App\ReleaseNoteGenerator
 */
class ReleaseNoteGeneratorTest extends TestCase
{
    use ProphecyTrait;

    private function getSut(ObjectProphecy $client) : ReleaseNoteGenerator
    {
        return new ReleaseNoteGenerator(
            $client->reveal(),
            '123',
            [],
        );
    }

    /**
     * @covers ::processMarkdown
     * @dataProvider markdownData
     */
    public function testProcessMarkdown(string $markdown, string $expected): void
    {
        $processed = $this->getSut($this->prophesize(Client::class))->processMarkdown($markdown);
        $this->assertSame($expected, $processed);
    }

    public function markdownData(): array
    {
        // @codingStandardsIgnoreStart
        return [
            [
                // Given markdown.
                "## [city-of-helsinki/drupal-hdbt](https://github.com/city-of-helsinki/drupal-hdbt): 4.3.0 to 4.3.2
### What's Changed
* Uhf x minor bug fixes2 by @Arkkimaagi in https://github.com/City-of-Helsinki/drupal-hdbt/pull/486
* UHF-7444: Add check for empty array item for metadata wrapper template by @teroelonen in https://github.com/City-of-Helsinki/drupal-hdbt/pull/484


**Full Changelog**: https://github.com/City-of-Helsinki/drupal-module-helfi-tpr/compare/2.1.3...2.1.4
## [city-of-helsinki/drupal-module-helfi-tunnistamo](https://github.com/city-of-helsinki/drupal-module-helfi-tunnistamo): 2.2.2 to 2.2.3
### What's Changed
* UHF-7565: Tunnistamo empty email by @ltpk in https://github.com/City-of-Helsinki/drupal-module-helfi-tunnistamo/pull/17

### New Contributors
* @ltpk made their first contribution in https://github.com/City-of-Helsinki/drupal-module-helfi-tunnistamo/pull/17

**Full Changelog**: https://github.com/City-of-Helsinki/drupal-module-helfi-tunnistamo/compare/2.2.2...2.2.3",
                // Expected markdown.
                "## [city-of-helsinki/drupal-hdbt](https://github.com/city-of-helsinki/drupal-hdbt): 4.3.0 to 4.3.2
### What's Changed
* Uhf x minor bug fixes2 in https://github.com/City-of-Helsinki/drupal-hdbt/pull/486
* [UHF-7444](https://helsinkisolutionoffice.atlassian.net/browse/UHF-7444): Add check for empty array item for metadata wrapper template in https://github.com/City-of-Helsinki/drupal-hdbt/pull/484


**Full Changelog**: https://github.com/City-of-Helsinki/drupal-module-helfi-tpr/compare/2.1.3...2.1.4
## [city-of-helsinki/drupal-module-helfi-tunnistamo](https://github.com/city-of-helsinki/drupal-module-helfi-tunnistamo): 2.2.2 to 2.2.3
### What's Changed
* [UHF-7565](https://helsinkisolutionoffice.atlassian.net/browse/UHF-7565): Tunnistamo empty email in https://github.com/City-of-Helsinki/drupal-module-helfi-tunnistamo/pull/17

**Full Changelog**: https://github.com/City-of-Helsinki/drupal-module-helfi-tunnistamo/compare/2.2.2...2.2.3"
            ]
        ];
        // @codingStandardsIgnoreEnd
    }
}
