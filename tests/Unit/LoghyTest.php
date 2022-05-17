<?php

declare(strict_types=1);

use Loghy\SDK\Exceptions\InvalidCodeException;
use Loghy\SDK\Exceptions\NotExpectedResponseException;

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
})->throws(InvalidCodeException::class)->with('invalid_code_response');

test('user() throws exception with unexpected response', function (array $response) {
    $loghy = new Loghy\SDK\Loghy(...$this->configuration);

    $client = makeGuzzleJsonMockClient($response);
    $loghy->setHttpClient($client);

    $loghy->setCode('__code__')->user();
})->throws(NotExpectedResponseException::class)->with('unexpected_response');

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
    array ...$data
): GuzzleHttp\Client {
    $mock = new GuzzleHttp\Handler\MockHandler(
        array_map(
            fn($d) => new GuzzleHttp\Psr7\Response(
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
