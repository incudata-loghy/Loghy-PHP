<?php

declare(strict_types=1);

namespace Loghy\SDK\Contract;

/**
 * Interface LoghyInterface
 */
interface LoghyInterface
{
    /**
     * Get Loghy ID from a authentication code
     * 
     * @param string $code
     * @return array<string,array|bool|int|string>|null
     */
    public function getLoghyId(string $code): ?array;

    /**
     * Get user information from a Loghy ID
     * 
     * @param string $loghyId
     * @return array<string,array|bool|int|string>|null
     */
    public function getUserInfo(string $loghyId): ?array;

    /**
     * Set user ID by site to a Loghy ID
     * 
     * @param string $loghyId
     * @param string $userId
     * @return array<string,bool|int|string>|null
     */
    public function putUserId(string $loghyId, string $userId): ?array;

    /**
     * Delete user information from a Loghy ID
     * 
     * @param string $loghyId
     * @return array<string,bool|int|string>|null
     */
    public function deleteUserInfo(string $loghyId): ?array;

    /**
     * Delete Loghy ID
     * 
     * @param string $loghyId
     * @return array<string,bool|int|string>|null
     */
    public function deleteLoghyId(string $loghyId): ?array;

    /**
     * Set Guzzle HTTP client
     * 
     * @param \GuzzleHttp\Client $client
     */
    public function setHttpClient(\GuzzleHttp\Client $client): void;

    /**
     * Get Guzzle HTTP Client
     * 
     * @return \GuzzleHttp\Client
     */
    public function httpClient(): \GuzzleHttp\Client;
}
