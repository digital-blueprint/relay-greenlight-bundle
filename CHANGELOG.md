# v0.2.10

* Update to api-platform v2.7

# v0.2.7

* Migrate to [dbp-relay-greenlight-bundle on GitHub](https://github.com/digital-blueprint/dbp-relay-greenlight-bundle)

# v0.2.6

* Register the DB entity manager with the core migration command

# v0.2.4

* Add a health check for the database connection
* migrations: only run migrations on the database the entity manager was
  configured for instead of all. Previously it would create empty tables in
  unrelated databases
* Add support for PHP 8.1
