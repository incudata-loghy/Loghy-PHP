<?php

declare(strict_types=1);

test('token() returns an array containing the access token and ID token', function (array $token) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($token, 200)
    );
    $loghy->setHttpClient($httpClient);

    expect($loghy->token('__authorization_code__'))->toEqual($token);
})->with('response.front.auth.token.ok');

test('token() throws exception when code is invalid', function (int $status, array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, $status)
    );
    $loghy->setHttpClient($httpClient);

    expect(
        fn() => $loghy->token('__wrong_code__')
    )->toThrow(
        \Loghy\SDK\Exception\LoghyException::class,
        $json['message']
    );
})->with('response.front.auth.token.error');
