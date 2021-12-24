<?php

declare(strict_types=1);

beforeEach(function(): void {
    $this->loghy = new Loghy\SDK\Loghy('__apiKey__', '__siteCode__');
});

test('httpClient() returns the same instance of the GuzzleHttp\Client class that was provided at setHttpClient()', function(): void {
    $client = new GuzzleHttp\Client();
    $this->loghy->setHttpClient($client);

    expect($this->loghy->httpClient())
        ->toBeInstanceOf(GuzzleHttp\Client::class)
        ->toEqual($client);
});

test('getLoghyId() returns an array has LoghyID', function(array $responseData): void {
    $client = makeGuzzleJsonMockClient($responseData);
    $this->loghy->setHttpClient($client);

    expect($this->loghy->getLoghyId('__code__'))
        ->toBeArray()
        ->toEqual($responseData);
})->with('loghy_id_response');

test('putUserId() returns an array has ok', function(array $responseData): void {
    $client = makeGuzzleJsonMockClient($responseData);
    $this->loghy->setHttpClient($client);

    expect($this->loghy->putUserId('__loghy_id__', '__user_id__'))
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
