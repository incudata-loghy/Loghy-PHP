<?php

declare(strict_types=1);

beforeEach(function (): void {
    $this->configuration = ['__apiKey__', '__siteCode__'];
});

test('httpClient() returns the same instance of the GuzzleHttp\Client class that was provided at setHttpClient()', function () {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);

    $client = new GuzzleHttp\Client();
    $loghy->setHttpClient($client);

    expect($loghy->httpClient())
        ->toBeInstanceOf(GuzzleHttp\Client::class)
        ->toEqual($client);
});


test('getCode() returns the code that was provided at setCode()', function () {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);
    $loghy->setCode('__code__');

    expect($loghy->getCode())
        ->toEqual('__code__');
});

test('user() throws exception with invalid code', function (array $response) {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);

    $client = makeGuzzleJsonMockClient($response);
    $loghy->setHttpClient($client);

    $loghy->setCode('__code__')->user();
})->with('invalid_code_response')->throws(RuntimeException::class);

test('user() throws exception with unsupported response', function (array $response) {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);

    $client = makeGuzzleJsonMockClient($response);
    $loghy->setHttpClient($client);

    $loghy->setCode('__code__')->user();
})->with('unsupported_response')->throws(RuntimeException::class, 'Invalid structure.');

test('user() throws exception without personal_data', function (array $res1) {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);

    $client = makeGuzzleJsonMockClient($res1, ['result' => true, 'data' => []]);
    $loghy->setHttpClient($client);

    $user = $loghy->setCode('__code__')->user();
})->with('loghy_id_response')->with('personal_data_response')
->throws(RuntimeException::class, 'Invalid structure.');

test('user() returns the User instance for the authenticated user', function (array $res1, array $res2) {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);

    $client = makeGuzzleJsonMockClient($res1, $res2);
    $loghy->setHttpClient($client);

    $user = $loghy->setCode('__code__')->user();
    expect($user)->toBeInstanceOf(\Loghy\SDK\User::class);
    expect($user->getId())->toEqual('xxxxxxxxxx');
    expect($user->getType())->toEqual('google');
    expect($user->getLoghyId())->toEqual('123');
    expect($user->getUserId())->toEqual('1');
    expect($user->getName())->toEqual('Taro Yamada');
    expect($user->getEmail())->toEqual('taro.yamada@example.com');
    expect($user->getRaw())->toEqual([
        'sid' => 'xxxxxxxxxx',
        'name' => 'Taro Yamada',
        'email' => 'taro.yamada@example.com',
    ]);
})->with('loghy_id_response')->with('personal_data_response');

function makeGuzzleJsonMockClient(
    array ...$data
): GuzzleHttp\Client {
    $mock = new GuzzleHttp\Handler\MockHandler(
        array_map(
            fn ($d) => new GuzzleHttp\Psr7\Response(
                200,
                ['Content-Type' => 'application/json; charset=UTF-8'],
                json_encode($d)
            ),
            $data
        )
    );

    $handlerStack = GuzzleHttp\HandlerStack::create($mock);
    return new GuzzleHttp\Client(['handler' => $handlerStack]);
}
