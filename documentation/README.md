# Documentation

## Generate automatic release changelog for project releases

Compares what's changed between two releases and generates a changelog accordingly.

### Webhook

- Payload URL: `https://webhook.drupal.hel.ninja/hooks/update-release-note`
- Content type: `application/json`
- See [Contact](#contact) for secret (`WEBHOOK_UPDATE_SECRET`).
- Select individual events: `Releases`.


## Generate automatic changelog for "Automatic updates" pull request

Generates a changelog for automatic updates.

See [documentation/automatic-updates.md](https://github.com/City-of-Helsinki/drupal-helfi-platform/blob/main/documentation/automatic-updates.md) for documentation about automatic updates.

### Webhook

- Payload URL: `https://webhook.drupal.hel.ninja/hooks/update-automation-pull-request`
- Content type: `application/json`
- See [Contact](#contact) for secret (`WEBHOOK_UPDATE_SECRET`).
- Select individual events: `Pull requests`.

## Development

*NOTE:* This is only required on the remote server.

Create .env file that contains:

```
GITHUB_OAUTH=your-github-oauth-token
# This is used to update individual packages
WEBHOOK_SECRET=your-webhook-secret
# This is used by this repository to trigger rebuilds
WEBHOOK_UPDATE_SECRET=your-webhook-secret
```
See [Composer repository](https://helsinkisolutionoffice.atlassian.net/wiki/spaces/HEL/pages/6501891919/Composer+repository) page on confluence for more information.


### Test webhooks locally

1. Copy request body from `Recent deliveries` tab of your repository
2. Save the request body to a `body.json` file
3. Generate X-Hub-Signature for your request body: `php -r "print hash_hmac('sha1', file_get_contents('body.json'), '{your webhook secret key here}');"`
4. Send the request: `curl -i -H 'Content-Type: application/json' -H "X-Hub-Signature: sha1={your hash_hmac from previous step }" -X POST https://helfi-webhook.docker.so/hooks/update-release-note --data-binary "@body.json"`
