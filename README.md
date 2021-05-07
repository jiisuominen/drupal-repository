# Drupal Composer repository

This is [composer](https://getcomposer.org/) repository to include custom Drupal modules/themes.

To use this, your `composer.json` should contain:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://repository.drupal.hel.ninja/"
    },
]
```

## Setting required env variables 

*NOTE:* This is only required on the remote server.

Create .env file that contains:

- `GITHUB_OAUTH=your-github-oauth-token`
- `WEBHOOK_SECRET=your-webhook-secret`

## Webhook to automatically update your package

Go to Settings -> Webhooks -> Add webhook

- Payload URL: `https://webhook.drupal.hel.ninja/hooks/update-index`
- Content type: `application/json`

Secret can be found from `helsinkiportaali` confluence or by contacting us directly. See [Contact](#contact).

## Contact

Slack: #helfi-drupal (http://helsinkicity.slack.com/)

Mail: helfi-drupal-aaaactuootjhcono73gc34rj2u@druid.slack.com
