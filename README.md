# Loghy PHP SDK

## Usage

### Installation

```
$ composer require --dev incudata-loghy/loghy-php
```

### SDK Initialization

```php
<?php
use Loghy\SDK\Loghy;

$loghy = new Loghy('{YOUR_API_KEY}', '{YOUR_SITE_CODE}');
```

### Get authenticated user from authentication code

```php
<?php

// ...

$user = $loghy->setCode($code)->user();

$loghyId = $user->getLoghyId();
$name    = $user->getName();
$email   = $user->getEmail();
```
