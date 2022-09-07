<?php

declare(strict_types=1);

it('user() returns the User instance for the authenticated user', function (array $token, array $userProfile) {
    $loghy = new \Loghy\SDK\Loghy('__site_code__');

    $httpClient = makeMockHttpClient(
        makeJsonResponse($token, 200),
        makeJsonResponse($userProfile, 200)
    );
    $loghy->setHttpClient($httpClient);

    $user = $loghy->user('__authorization_code__');
    expect($user->getId())->toEqual($userProfile['user']['sub']);
    expect($user->getType())->toEqual($userProfile['user']['social_provider']);
    expect($user->getLoghyId())->toEqual($userProfile['user']['loghy_id']);
    expect($user->getUserId())->toEqual($userProfile['user']['user_id']);
    expect($user->getName())->toEqual($userProfile['user']['name']);
    expect($user->getEmail())->toEqual($userProfile['user']['email']);
    expect($user->getRaw($userProfile['user']));
})->with('response.front.auth.token.ok')->with('response.front.auth.verify.ok');
