# Loghy PHP SDK

## Usage

### Installation

```
$ composer require incudata-loghy/loghy-php
```

### SDK Initialization

```php
<?php
use Loghy\SDK\Loghy;

$loghy = new Loghy('{YOUR_SITE_CODE}');
```

### Get authenticated user from authorization code

```php
<?php

$user = $loghy->user($code);

$loghyId = $user->getLoghyId();
$name    = $user->getName();
$email   = $user->getEmail();
```

### Set the user ID issued by your sites to Loghy ID

```php
$loghy->putUserId('{YOUR_USER_ID}', '{YOUR_LOGHY_ID}');
```
