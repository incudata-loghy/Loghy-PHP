<?php

declare(strict_types=1);

namespace Loghy\SDK;

use GuzzleHttp\Client;
use Loghy\SDK\Contract\LoghyInterface;
use Loghy\SDK\Contract\User as ContractUser;
use Loghy\SDK\Exception\InvalidResponseBodyStructureException;
use Loghy\SDK\Exception\LoghyException;
use Loghy\SDK\Exception\UnsetCodeException;

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
        if (!$this->client) {
            $this->client = new Client(['base_uri' => $this->getApiUri()]);
        }
        return $this->client;
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
     * @throws \Loghy\SDK\Exception\UnsetCodeException
     */
    public function getCode(): string
    {
        return $this->code ?? throw new UnsetCodeException(
            'The authentication code has not been set. ' .
            'Please call the setCode() method to set up.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\UnsetCodeException
     * @throws \Loghy\SDK\Exception\InvalidResponseBodyStructureException
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
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\InvalidResponseBodyStructureException
     */
    protected function getLoghyId(
        string $code
    ): array {
        $data = [ 'code' => $code ];
        $response = $this->httpClient()->request('POST', 'loghyid', [
            'form_params' => $data
        ]);

        $body = (string) $response->getBody();
        $content = json_decode($body, true);

        return $this->verifyResponse($content);
    }

    /**
     * Get user information from a Loghy ID
     *
     * @param string $loghyId
     * @return array<string,array|bool|int|string>
     *
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\InvalidResponseBodyStructureException
     */
    protected function getUserInfo(
        string $loghyId
    ): array {
        $response = $this->requestApi('lgid2get', $loghyId);
        $data = $this->verifyResponse($response);

        return $data['personal_data'] ?? throw new InvalidResponseBodyStructureException(
            'Data key value has no personal_data key.',
            $response
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\InvalidResponseBodyStructureException
     */
    public function putUserId(string $userId, string $loghyId = null): bool
    {
        $loghyId = $loghyId ?? $this->user()->getLoghyId();
        $response = $this->requestApi('lgid2set', $loghyId, $userId);

        $this->verifyResponse($response, false);

        $this->user = ($this->user ?? new User())->map([
            'loghyId' => $loghyId,
            'userId' => $userId,
        ]);
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\InvalidResponseBodyStructureException
     */
    public function deleteUser(string $loghyId = null): bool
    {
        $loghyId = $loghyId ?? $this->user()->getLoghyId();
        $response = $this->requestApi('lgid2del', $loghyId);

        $this->verifyResponse($response, false);

        $this->user = null;
        return true;
    }

    /**
     * @param array $response
     * @param bool $hasData
     * @return array|bool
     *
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\InvalidResponseBodyStructureException
     */
    private function verifyResponse(array $response, bool $hasData = true): array|bool
    {
        if (!isset($response['result'])) {
            throw new InvalidResponseBodyStructureException('The response json has no result key.', $response);
        }
        if (!is_bool($response['result'])) {
            throw new InvalidResponseBodyStructureException('Result key value is not bool.', $response);
        }
        if ($hasData) {
            if (!isset($response['data'])) {
                throw new InvalidResponseBodyStructureException('The response json has no data key.', $response);
            }
            if (!is_array($response['data'])) {
                throw new InvalidResponseBodyStructureException('Data key value is not array.', $response);
            }
        }

        if ($response['result']) {
            if ($hasData) {
                return $response['data'];
            }
            return true;
        }

        throw new LoghyException(
            $response['error_message'] ?? '',
            $response['error_code'] ?? 0
        );
    }

    protected function getApiUri(): string
    {
        return 'https://api001.sns-loghy.jp/api/';
    }

    /**
     * Request API
     *
     * @param string $command
     * @param string $id
     * @param string $mid
     * @return array|null
     */
    private function requestApi(
        string $command,
        string $id,
        string $mid = ''
    ): ?array {
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

        $response = $this->httpClient()->request('GET', $command, [
            'query' => $data
        ]);

        $body = (string) $response->getBody();
        $content = json_decode($body, true);
        return $content;
    }
}
