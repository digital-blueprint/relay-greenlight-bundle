# API

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
