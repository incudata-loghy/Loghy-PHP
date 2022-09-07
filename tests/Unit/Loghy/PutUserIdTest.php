<?php

declare(strict_types=1);

test('putUserId() sets user_id to user', function (array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, 200)
    );
    $loghy->setHttpClient($httpClient)
        ->setSiteAccessToken('__site_access_token__');

    expect($loghy->putUserId('__user_id__', '__loghy_id__'))
        ->toBe(true);
})->with('response.manage.user.bulk.put.ok');

test('putUserId() throws exception when some error occurs', function (int $status, array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, $status)
    );
    $loghy->setHttpClient($httpClient)
        ->setSiteAccessToken('__site_access_token__');

    expect(
        fn () => $loghy->putUserId('__user_id__', '__loghy_id__')
    )->toThrow(
        \Loghy\SDK\Exception\LoghyException::class,
        $json['message']
    );
})->with('response.manage.user.bulk.put.error');

test('putUserId() throws exception when token has not been set', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->putUserId('__user_id__', '__loghy_id__');
})->throws(
    \Loghy\SDK\Exception\UnsetSiteAccessTokenException::class,
    'The site access token has not been set. Please call the setSiteAccessToken() method to set up.',
);

test('putUserId() throws exception when LoghyID has not been set', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->setSiteAccessToken('__site_access_token__');

    $loghy->putUserId('__user_id__', null);
})->throws(
    \Loghy\SDK\Exception\UnsetLoghyIdException::class,
    'Loghy ID has not been set. Please call putUserId() method with Loghy ID as an argument.'
);

test('putUserId() can be used without Loghy ID when it has authenticated user', function (array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, 200),
    );
    $loghy->setHttpClient($httpClient)
        ->setSiteAccessToken('__site_access_token__')
        ->setUser(
            (new \Loghy\SDK\User)->map(['loghyId' => '__loghy_id__'])
        );

    expect($loghy->putUserId('__user_id__', null))
        ->toBe(true);
})->with('response.manage.user.bulk.put.ok');
