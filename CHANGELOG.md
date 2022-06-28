# v0.2.4

* Add a health check for the database connection
* migrations: only run migrations on the database the entity manager was
  configured for instead of all. Previously it would create empty tables in
  unrelated databases
* Add support for PHP 8.1
