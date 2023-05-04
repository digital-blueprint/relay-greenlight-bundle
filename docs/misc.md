# Miscellaneous

## Roles

This bundle needs the role `ROLE_SCOPE_GREENLIGHT` assigned to the user to get permissions for the api.

## Cleanup

Run this script daily to remove expired permits.

```bash
php bin/console dbp:relay-greenlight:cleanup
```

## Database migration

Run this script to migrate the database. Run this script after installation of the bundle and
after every update to adapt the database to the new source code.

```bash
php bin/console doctrine:migrations:migrate --em=dbp_relay_greenlight_bundle
```
