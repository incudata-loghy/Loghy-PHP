<?php

declare(strict_types=1);

namespace Loghy\SDK;

use GuzzleHttp\Client;
use Loghy\SDK\Contract\LoghyInterface;

/**
 * Class Loghy.
 */
class Loghy implements LoghyInterface {

    /**
     * The Guzzle client instance.
     */
    protected ?Client $client;

    function __construct(
        private string $apiKey,
        private string $siteCode
    ) {
    }

    /**
     * Get Loghy ID from a authentication code
     * 
     * @param string $code
     * @return array<string,array|bool|int|string>|null
     */
    public function getLoghyId(
        string $code
    ): ?array {
        $url = 'https://api001.sns-loghy.jp/api/' . 'loghyid';
        $data = [ 'code' => $code ];

        $response = $this->httpClient()->request('POST', $url, [
            'form_params' => $data
        ]);

        $body = (string) $response->getBody();
        $content = json_decode($body, true);
        return $content;
    }

    /**
     * Get user information from a Loghy ID
     * 
     * @param string $loghyId
     * @return array<string,array|bool|int|string>|null
     */
    public function getUserInfo(
        string $loghyId
    ): ?array {
        return $this->requestApi('lgid2get', $loghyId);
    }

    /**
     * Set user ID by site to a Loghy ID
     * 
     * @param string $loghyId
     * @param string $userId
     * @return array<string,bool|int|string>|null
     */
    public function putUserId(
        string $loghyId,
        string $userId
    ): ?array {
        return $this->requestApi('lgid2set', $loghyId, $userId);
    }

    /**
     * Delete user ID by site from a Loghy ID
     * 
     * @param string $loghyId
     * @return array<string,bool|int|string>|null
     */
    public function deleteUserId(
        string $loghyId
    ): ?array {
        return $this->requestApi('lgid2sdel', $loghyId);
    }

    /**
     * Delete user information from a Loghy ID
     * 
     * @param string $loghyId
     * @return array<string,bool|int|string>|null
     */
    public function deleteUserInfo(
        string $loghyId
    ): ?array {
        return $this->requestApi('lgid2pdel', $loghyId);
    }
    
    /**
     * Delete Loghy ID
     * 
     * @param string $loghyId
     * @return array<string,bool|int|string>|null
     */
    public function deleteLoghyId(
        string $loghyId
    ): ?array {
        return $this->requestApi('lgid2del', $loghyId);
    }

    /**
     * Request API
     * 
     * @param string $command
     * @param string $id
     * @param string $mid
     * @return array<string,array|bool|int|string>|null
     */
    private function requestApi(
        string $command,
        string $id,
        string $mid = ''
    ): ?array {
        $url = 'https://api001.sns-loghy.jp/api/' . $command;

        $atype = 'site';
        $time = time();
        $skey = hash(
            'sha256',
            $command . $atype . $this->siteCode . $id . $mid . $time . $this->apiKey
        );
        $data = [
            'cmd' => $command,
            'atype' => $atype,
            'sid' => $this->siteCode,
            'id' => $id,
            'mid' => $mid,
            'time' => $time,
            'skey' => $skey,
        ];

        $response = $this->httpClient()->request('GET', $url, [
            'query' => $data
        ]);

        $body = (string) $response->getBody();
        $content = json_decode($body, true);
        return $content;
    }

    /**
     * Set Guzzle HTTP client
     * 
     * @param \GuzzleHttp\Client $client
     */
    public function setHttpClient(
        Client $client
    ): void {
        $this->client = $client;
    }

    /**
     * Get Guzzle HTTP Client
     * 
     * @return \GuzzleHttp\Client
     */
    public function httpClient(): Client
    {
        return $this->client ?? new Client();
    }
}
