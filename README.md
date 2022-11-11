# Drupal Composer repository

This is composer repository used to distribute dependencies as part of [City-of-Helsinki/drupal-helfi-platform](https://github.com/City-of-Helsinki/drupal-helfi-platform) ecosystem.

To use this, your `composer.json` should contain:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://repository.drupal.hel.ninja/"
    },
]
```

## Adding a new package 

Your package must contain a `composer.json` file.

- Add your package to [satis.json](/satis.json) file.
- Add the required webhook: [Update composer repository](#update-composer-repository).

## Available webhooks 

### Update composer repository

In order for composer to figure out what packages have changed, the package index needs to be rebuilt on every commit.

Go to Settings -> Webhooks -> Add webhook

- Payload URL: `https://webhook.drupal.hel.ninja/hooks/update-index`
- Content type: `application/json`
- Events: `Send everything`
- Secret can be found on [Composer repository](https://helsinkisolutionoffice.atlassian.net/wiki/spaces/HEL/pages/6501891919/Composer+repository) confluence page.

## Documentation

See [documentation](/documentation) for more documentation.

## Contact

Slack: #helfi-drupal (http://helsinkicity.slack.com/)

Mail: helfi-drupal-aaaactuootjhcono73gc34rj2u@druid.slack.com
