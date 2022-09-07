<?php

declare(strict_types=1);

test('deleteUser() deletes user', function (array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, 200)
    );
    $loghy->setHttpClient($httpClient)
        ->setSiteAccessToken('__site_access_token__');

    expect($loghy->deleteUser('__loghy_id__'))
        ->toBe(true);
})->with('response.manage.user.bulk.delete.ok');

test('deleteUser() throws exception when some error occurs', function (int $status, array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, $status)
    );
    $loghy->setHttpClient($httpClient)
        ->setSiteAccessToken('__site_access_token__');

    expect(
        fn () => $loghy->deleteUser('__loghy_id__')
    )->toThrow(
        \Loghy\SDK\Exception\LoghyException::class,
        $json['message']
    );
})->with('response.manage.user.bulk.delete.error');

test('deleteUser() throws exception when token has not been set', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->deleteUser('__loghy_id__');
})->throws(
    \Loghy\SDK\Exception\UnsetSiteAccessTokenException::class,
    'The site access token has not been set. Please call the setSiteAccessToken() method to set up.',
);

test('deleteUser() throws exception when LoghyID has not been set', function () {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $loghy->setSiteAccessToken('__site_access_token__');

    $loghy->deleteUser(null);
})->throws(
    \Loghy\SDK\Exception\UnsetLoghyIdException::class,
    'Loghy ID has not been set. Please call deleteUser() method with Loghy ID as an argument.'
);

test('deleteUser() can be used without Loghy ID when it has authenticated user', function (array $json) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($json, 200)
    );
    $loghy->setHttpClient($httpClient)
        ->setSiteAccessToken('__site_access_token__')
        ->setUser(
            (new \Loghy\SDK\User)->map(['loghyId' => '__loghy_id__'])
        );

    expect($loghy->deleteUser('__loghy_id__'))
        ->toBe(true);
})->with('response.manage.user.bulk.delete.ok');
