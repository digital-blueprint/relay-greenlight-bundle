# DbpRelayGreenlightBundle

## Integration into the API Server

* Add the repository to your composer.json:

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "git@gitlab.tugraz.at:dbp/relay/dbp-relay-greenlight-bundle.git"
        }
    ],
```

* Add the bundle package as a dependency:

```
composer require dbp/relay-greenlight-bundle=dev-main
```

* Add the bundle to your `config/bundles.php`:

```php
...
Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
Dbp\Relay\GreenlightBundle\DbpRelayGreenlightBundle::class => ['all' => true],
DBP\API\CoreBundle\DbpCoreBundle::class => ['all' => true],
];
```

* Run `composer install` to clear caches

## Configuration

The bundle has a `database_url` configuration value that you can specify in your
app, either by hardcoding it, or by referencing an environment variable.

For this create `config/packages/dbp_relay_greenlight.yaml` in the app with the following
content:

```yaml
dbp_relay_greenlight:
  database_url: 'mysql://db:secret@mariadb:3306/db?serverVersion=mariadb-10.3.30'
  # database_url: %env(EU_DCC_DATABASE_URL)%
```

The value gets read in `DbpRelayGreenlightExtension` and passed when creating the
`MyCustomService` service.

For more info on bundle configuration see
https://symfony.com/doc/current/bundles/configuration.html

## Development & Testing

* Install dependencies: `composer install`
* Run tests: `composer test`
* Run linters: `composer run lint`
* Run cs-fixer: `composer run cs-fix`

## Bundle dependencies

Don't forget you need to pull down your dependencies in your main application if you are installing packages in a bundle.

```bash
# updates and installs dependencies from dbp/relay-greenlight-bundle
composer update dbp/relay-greenlight-bundle
```

## Scripts

### Cleanup

Run this script daily to remove expired permits.

```bash
php bin/console dbp:relay-greenlight:cleanup
```

### Database migration

Run this script to migrate the database.

```bash
php bin/console doctrine:migrations:migrate --em=dbp_relay_greenlight_bundle
```

## Error codes

### General

| relay:errorId                         | Description                  | relay:errorDetails | Example |
| ------------------------------------- | ---------------------------- | ------------------ | ------- |
| `greenlight:current-person-not-found` | Current person wasn't found. |                    |         |

### `/greenlight/permits`

#### POST

| relay:errorId                                 | Description                                                         | relay:errorDetails | Example |
| --------------------------------------------- | ------------------------------------------------------------------- | ------------------ | ------- |
| `greenlight:additional-information-not-valid` | The content of the `additionalInformation` attribute was not valid. |                    |         |

### `/greenlight/permits/{identifier}`

#### GET

| relay:errorId                           | Description                             | relay:errorDetails | Example |
| --------------------------------------- | --------------------------------------- | ------------------ | ------- |
| `greenlight:permit-not-found`           | Permit was not found.                   |                    |         |
| `greenlight:person-does-not-own-permit` | Current person doesn't own this permit. |                    |         |
