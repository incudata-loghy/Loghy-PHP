<?php

declare(strict_types=1);

test('socialProviderTokens() returns an array containing tokens issued by social providers', function (array $tokens) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($tokens, 200)
    );
    $loghy->setHttpClient($httpClient);

    expect($loghy->socialProviderTokens('__access_token__'))->toEqual($tokens);
})->with('response.front.user.social_provider_token.ok');

test('socialProviderTokens() throws exception when the access token is invalid', function (int $status, array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, $status)
    );
    $loghy->setHttpClient($httpClient);

    expect(
        fn () => $loghy->socialProviderTokens('__wrong_access_token__')
    )->toThrow(
        \Loghy\SDK\Exception\LoghyException::class,
        $json['message']
    );
})->with('response.front.auth.token.error');
