# Configuration

The bundle has a `database_url` configuration value that you can specify in your
app, either by hardcoding it, or by referencing an environment variable.

For this create `config/packages/dbp_relay_greenlight.yaml` in the app with the following
content:

```yaml
dbp_relay_greenlight:
  database_url: 'mysql://db:secret@mariadb:3306/db?serverVersion=mariadb-10.3.30'
  # database_url: %env(EU_DCC_DATABASE_URL)%
```

If you were using the [DBP API Server Template](https://github.com/digital-blueprint/relay-server-template)
as template for your Symfony application, then the configuration file should have already been generated for you.

For more info on bundle configuration see <https://symfony.com/doc/current/bundles/configuration.html>.

## PersonPho

## Bundle Configuration

Created via `./bin/console config:dump-reference DbpRelayGreenlightBundle | sed '/^$/d'`

```yaml
# Default configuration for "DbpRelayGreenlightBundle"
dbp_relay_greenlight:
    database_url:         ~
```
