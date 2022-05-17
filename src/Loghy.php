<?php

declare(strict_types=1);

namespace Loghy\SDK;

use GuzzleHttp\Client;
use Loghy\SDK\Contract\LoghyInterface;
use Loghy\SDK\Contract\User as ContractUser;
use RuntimeException;

/**
 * Class Loghy.
 */
class Loghy implements LoghyInterface
{
    /**
     * The Guzzle client instance.
     */
    protected ?Client $client;

    /**
     * The authorization code.
     */
    protected ?string $code = null;

    /**
     * The cached user instance.
     */
    protected ?User $user = null;


    public function __construct(
        private string $apiKey,
        private string $siteCode
    ) {
    }

    /**
     * Set Guzzle HTTP client
     *
     * @param \GuzzleHttp\Client $client
     */
    public function setHttpClient(
        Client $client
    ): self {
        $this->client = $client;
        return $this;
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

    /**
     * {@inheritdoc}
     */
    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get the authorization code.
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function user(): ContractUser
    {
        if ($this->user) {
            return $this->user;
        }
        $this->user ??= new User();

        $data = $this->getLoghyId($this->getCode());
        $this->user->map([
            'type' => $data['social_login'] ?? null,
            'loghyId' => isset($data['lgid']) ? (string)$data['lgid'] : null,
            'userId' => $data['site_id'] ?? null,
        ]);

        $userInfo = $this->getUserInfo($this->user->getLoghyId());
        $this->user->map([
            'id' => $userInfo['sid'] ?? null,
            'name' => $userInfo['name'] ?? null,
            'email' => $userInfo['email'] ?? null,
        ])->setRaw($userInfo);

        return $this->user;
    }

    /**
     * Get Loghy ID response from a authentication code.
     *
     * @param string $code
     * @return array
     *
     * @throws \Loghy\SDK\Exceptions\InvalidCodeException
     * @throws \Loghy\SDK\Exceptions\NotExpectedResponseException
     */
    protected function getLoghyId(
        string $code
    ): array {
        // $url = 'https://api001.sns-loghy.jp/api/' . 'loghyid';
        $url = 'http://localhost:8081/api/' . 'loghyid'; // DEBUG
        $data = [ 'code' => $code ];
        $response = $this->httpClient()->request('POST', $url, [
            'form_params' => $data
        ]);

        $body = (string) $response->getBody();
        $content = json_decode($body, true);

        return $this->verifyDataResponse($content);
    }

    /**
     * Get user information from a Loghy ID
     *
     * @param string $loghyId
     * @return array<string,array|bool|int|string>|null
     */
    protected function getUserInfo(
        string $loghyId
    ): ?array {
        $response = $this->requestApi('lgid2get', $loghyId);
        $data = $this->verifyDataResponse($response);

        return $data['personal_data'] ?? throw new RuntimeException('Invalid structure.');
    }

    /**
     * {@inheritdoc}
     */
    public function putUserId(string $loghyId, string $userId): bool
    {
        // TODO
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLoghyId(string $loghyId): bool
    {
        // TODO
        return false;
    }

    /**
     *
     */
    private function verifyDataResponse(array $response): array
    {
        if (!isset($response['result']) || !isset($response['data'])) {
            throw new RuntimeException('Invalid structure.');
        }

        if ($response['result']) {
            return $response['data'];
        }

        throw new RuntimeException(
            $response['error_message'] ?? '',
            $response['error_code'] ?? 0
        );
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
        // $url = 'https://api001.sns-loghy.jp/api/' . $command;
        $url = 'http://localhost:8081/api/' . $command; // DEBUG

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
}
