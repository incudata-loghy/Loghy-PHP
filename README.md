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

### Get Loghy ID from authentication code

```php
<?php

$res = $loghy->getLoghyId($code);
$data = $res['data'] ?? null;
$loghyId = $data['lgid'] ?? null;
```
