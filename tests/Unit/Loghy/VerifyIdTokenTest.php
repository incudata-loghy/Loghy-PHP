<?php

declare(strict_types=1);

test('verify() returns an array containing user profile', function (array $response) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($response, 200)
    );
    $loghy->setHttpClient($httpClient);

    expect($loghy->verify('__ID_token__'))->toEqual($response);
})->with('response.front.auth.verify.ok');

test('verify() throws exception when some error occurs', function (int $status, array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, $status)
    );
    $loghy->setHttpClient($httpClient);

    expect(
        fn () => $loghy->verify('__wrong_ID_token__')
    )->toThrow(
        \Loghy\SDK\Exception\LoghyException::class,
        $json['message']
    );
})->with('response.front.auth.verify.error');
