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

test('user() returns the User instance for the authenticated user', function () {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);
    $user = $loghy->setCode('__code__')->user();
});


////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////


test('getLoghyId() returns an array has LoghyID', function (array $responseData) {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);

    $client = makeGuzzleJsonMockClient($responseData);
    $loghy->setHttpClient($client);

    expect($loghy->getLoghyId('__code__'))
        ->toBeArray()
        ->toEqual($responseData);
})->with('loghy_id_response');


test('putUserId() returns an array has ok', function (array $responseData) {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);

    $client = makeGuzzleJsonMockClient($responseData);
    $loghy->setHttpClient($client);

    expect($loghy->putUserId('__loghy_id__', '__user_id__'))
        ->toBeArray()
        ->toEqual($responseData);
})->with('ok_response');


function makeGuzzleJsonMockClient(
    array $data
): GuzzleHttp\Client {
    $mock = new GuzzleHttp\Handler\MockHandler([
        new GuzzleHttp\Psr7\Response(
            200,
            ['Content-Type' => 'application/json; charset=UTF-8'],
            json_encode($data)
        )
    ]);

    $handlerStack = GuzzleHttp\HandlerStack::create($mock);
    return new GuzzleHttp\Client(['handler' => $handlerStack]);
}
