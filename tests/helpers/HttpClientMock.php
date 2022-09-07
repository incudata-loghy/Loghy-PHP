<?php

/**
 * Make JSON response.
 *
 * @param array $json
 * @param int $status
 *
 * @return \GuzzleHttp\Psr7\Response
 */
function makeJsonResponse(array $json = [], int $status = 200): \GuzzleHttp\Psr7\Response
{
    return new \GuzzleHttp\Psr7\Response(
        $status,
        ['Content-Type' => 'application/json; charset=UTF-8'],
        json_encode($json)
    );
}

/**
 * Make mock HTTP client.
 *
 * @param \GuzzleHttp\Psr7\Response[] $responses
 */
function makeMockHttpClient(
    \GuzzleHttp\Psr7\Response ...$responses
): GuzzleHttp\Client {
    $mock = new GuzzleHttp\Handler\MockHandler($responses);
    $handlerStack = GuzzleHttp\HandlerStack::create($mock);
    return new GuzzleHttp\Client(['handler' => $handlerStack]);
}
