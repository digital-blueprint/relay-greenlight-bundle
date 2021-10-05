# DbpRelayGreenlightBundle

[GitLab](https://gitlab.tugraz.at/dbp/greenlight/dbp-relay-greenlight-bundle) | [Packagist](https://packagist.org/packages/dbp/relay-greenlight-bundle)

This bundle allows you to create permits for the Covid19 certificate evaluation process.

You will need a database that is compatible with Doctrine (for example MariaDB) to store the permits.

## Bundle installation

### For Development

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

```bash
composer require dbp/relay-greenlight-bundle=dev-main
```

### For production

If you don't want to install the bundle directly from git you can install it from
[packagist.org](https://packagist.org/packages/dbp/relay-greenlight-bundle).

```bash
composer require dbp/relay-greenlight-bundle
```

## Integration into the API Server

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

For more info on bundle configuration see <https://symfony.com/doc/current/bundles/configuration.html>.

## PersonPhotoProvider service

For this bundle to work you need to create a service that implements
[PersonPhotoProviderInterface](https://gitlab.tugraz.at/dbp/greenlight/dbp-relay-greenlight-bundle/-/blob/main/src/API/PersonPhotoProviderInterface.php)
in your application.

### Example

#### Service class

You can for example put below code into `src/Service/PersonPhotoProvider.php`:

```php
declare(strict_types=1);

namespace YourUniversity\Service;

use DBP\API\BaseBundle\Entity\Person;
use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;

class PersonPhotoProvider implements PersonPhotoProviderInterface
{
    /**
     * Returns the photo of a person as binary data.
     */
    public function getPhotoData(Person $person): string
    {
        // TODO: Add some code to fetch the photo for $person
        $data = your_fetch_method($person);

        return $data;
    }
}
```

#### Services configuration

For above class you need to add this to your `src/Resources/config/services.yaml`:

```yaml
  Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface:
    '@YourUniversity\Service\PersonPhotoProvider'
```

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

Run this script to migrate the database. Run this script after installation of the bundle and
after every update to adapt the database to the new source code.

```bash
php bin/console doctrine:migrations:migrate --em=dbp_relay_greenlight_bundle
```

## Error codes

### General

| relay:errorId                         | Status code | Description                  | relay:errorDetails | Example |
| ------------------------------------- | ----------- | ---------------------------- | ------------------ | ------- |
| `greenlight:current-person-not-found` | 403         | Current person wasn't found. |                    |         |

### `/greenlight/permits`

#### POST

| relay:errorId                                   | Status code | Description                                                                | relay:errorDetails | Example                          |
| ----------------------------------------------- | ----------- | -------------------------------------------------------------------------- | ------------------ | -------------------------------- |
| `greenlight:additional-information-not-valid`   | 400         | The content of the `additionalInformation` attribute was not valid.        |                    |                                  |
| `greenlight:additional-information-not-decoded` | 403         | The content of the `additionalInformation` attribute could not be decoded. |                    |                                  |
| `greenlight:current-person-no-photo`            | 503         | Photo for current person could not be loaded!                              |                    |                                  |
| `greenlight:current-person-not-found`           | 403         | Current person wasn't found.                                               |                    |                                  |
| `greenlight:consent-assurance-not-true`         | 400         | The content of the `consentAssurance` attribute was not true.              |                    |                                  |
| `greenlight:permit-not-created`                 | 500         | The permit could not be created.                                           | `message`          | `['message' => 'Error message']` |
| `greenlight:photo-service-error`                | 500         | The photo service had an error!                                            | `message`          | `['message' => 'Error message']` |

### `/greenlight/permits/{identifier}`

#### GET

| relay:errorId                           | Status code | Description                             | relay:errorDetails | Example |
| --------------------------------------- | ----------- | --------------------------------------- | ------------------ | ------- |
| `greenlight:permit-not-found`           | 404         | Permit was not found.                   |                    |         |
| `greenlight:person-does-not-own-permit` | 403         | Current person doesn't own this permit. |                    |         |

## Roles

This bundle needs the role `ROLE_SCOPE_GREENLIGHT` assigned to the user to get permissions for the api.
