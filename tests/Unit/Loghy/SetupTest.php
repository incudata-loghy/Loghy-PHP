<?php

declare(strict_types=1);

test('httpClient() return the instance of GuzzleHttp\Client class', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    expect($loghy->httpClient())
        ->toEqual(
            new \GuzzleHttp\Client([
                'base_uri' => 'https://api001.sns-loghy.jp/api/v2/'
            ])
        );
});

test('httpClient() returns the same instance of the GuzzleHttp\Client class that was provided at setHttpClient', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');
    $client = new \GuzzleHttp\Client();

    $loghy->setHttpClient($client);
    expect($loghy->httpClient())
        ->toBe($client);
});

test('getCode() returns the code that was provided at setCode()', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->setCode('__authorization_code__');
    expect($loghy->getCode())
        ->toBe('__authorization_code__');
});

test('getCode() throws exception when the code has not been set', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->getCode();
})->throws(
    \Loghy\SDK\Exception\UnsetCodeException::class,
    'The authorization code has not been set. Please call the setCode() method to set up.',
);

test('token() returns the same array that was provided at setToken()', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->setToken(['token' => '...']);
    expect($loghy->token())
        ->toBe(['token' => '...']);
});

test('getSiteAccessToken() returns the same value that was provided at setSiteAccessToken()', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->setSiteAccessToken('__site_access_token__');
    expect($loghy->getSiteAccessToken())
        ->toBe('__site_access_token__');
});

test('getSiteAccessToken() throws exception when the token has not been set', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->getSiteAccessToken();
})->throws(
    \Loghy\SDK\Exception\UnsetSiteAccessTokenException::class,
    'The site access token has not been set. Please call the setSiteAccessToken() method to set up.',
);
