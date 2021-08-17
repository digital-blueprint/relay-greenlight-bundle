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

The bundle has a `secret_token` configuration value that you can specify in your
app, either by hardcoding it, or by referencing an environment variable.

For this create `config/packages/dbp_relay_greenlight.yaml` in the app with the following
content:

```yaml
dbp_relay_greenlight:
  secret_token: 42
  # secret_token: '%env(SECRET_TOKEN)%'
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
