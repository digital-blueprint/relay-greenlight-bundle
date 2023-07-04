# DbpRelayGreenlightBundle

[GitHub](https://github.com/digital-blueprint/relay-greenlight-bundle) |
[Packagist](https://packagist.org/packages/dbp/relay-greenlight-bundle) |
[Frontend Application](https://github.com/digital-blueprint/greenlight-app) |
[Greenlight Website](https://handbook.digital-blueprint.org/blueprints/greenlight)

[![Test](https://github.com/digital-blueprint/relay-greenlight-bundle/actions/workflows/test.yml/badge.svg)](https://github.com/digital-blueprint/relay-greenlight-bundle/actions/workflows/test.yml)

**Note:** This project depends on the DCC infrastructure of the Austrian
Government. Since the DCC infrastructure is [no longer available since June
2023](https://github.com/Federal-Ministry-of-Health-AT/green-pass-overview/issues/11#issuecomment-1617997232),
this project is no longer actively maintained.

This bundle allows you to create permits for the Covid19 certificate evaluation process.

```bash
composer require dbp/relay-greenlight-bundle
```

See the [documentation](./docs/README.md) for more information and the
[developer documentation](./docs-dev/README.md) for how to extend the bundle.

## Development & Testing

* Install dependencies: `composer install`
* Run tests: `composer test`
* Run linters: `composer run lint`
* Run cs-fixer: `composer run cs-fix`
