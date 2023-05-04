# Development Overview

## PersonPhotoProvider service

For this bundle to work you need to create a service that implements
[PersonPhotoProviderInterface](https://github.com/digital-blueprint/relay-greenlight-bundle/blob/main/src/API/PersonPhotoProviderInterface.php)
in your application.

### Example

#### Service class

You can for example put below code into `src/Service/PersonPhotoProvider.php`:

```php
<?php

declare(strict_types=1);

namespace YourUniversity\Service;

use Dbp\Relay\BasePersonBundle\Entity\Person;
use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;

class PersonPhotoProvider implements PersonPhotoProviderInterface
{
    /**
     * Returns the photo of the current user as binary data.
     */
    public function getPhotoDataForCurrentUser(): string;
    {
        // TODO: Add some code to fetch current user
        $user = your_user_fetch_method();

        // TODO: Add some code to fetch the photo for $user
        $data = your_image_fetch_method($user);

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