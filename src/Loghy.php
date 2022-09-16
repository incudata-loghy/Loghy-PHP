<?php

declare(strict_types=1);

namespace Loghy\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Loghy\SDK\Contract\LoghyInterface;
use Loghy\SDK\Exception\InvalidResponseBodyStructureException;
use Loghy\SDK\Exception\LoghyException;
use Loghy\SDK\Exception\UnsetCodeException;
use Loghy\SDK\Exception\UnsetLoghyIdException;
use Loghy\SDK\Exception\UnsetSiteAccessTokenException;
use Loghy\SDK\Exception\UnsetUserAccessTokenException;

/**
 * Class Loghy.
 */
class Loghy implements LoghyInterface
{
    /**
     * The Guzzle client instance.
     */
    protected ?Client $client = null;

    /**
     * The authorization code.
     */
    protected ?string $code = null;

    /**
     * The user token issued by Loghy
     */
    protected ?array $userToken = null;

    /**
     * The site access token required to use management(admin) API.
     */
    protected ?string $siteAccessToken = null;

    /**
     * The cached user instance.
     */
    protected ?User $user = null;


    public function __construct(
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
     *
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\UnsetCodeException
     * @throws \Loghy\SDK\Exception\InvalidResponseBodyStructureException
     */
    public function user(string $code = null): \Loghy\SDK\User
    {
        if ($this->user) {
            return $this->user;
        }
        $this->user ??= new User();

        $code and $this->setCode($code);

        $tokenResponse = $this->token($this->getCode());
        $idToken = $tokenResponse['id_token']
            ?? throw new InvalidResponseBodyStructureException("auth/token response has no id_token key", $tokenResponse);

        $verifyResponse = $this->verify($idToken);
        $userRaw = $verifyResponse['user']
            ?? throw new InvalidResponseBodyStructureException("auth/verify response has not user key", $verifyResponse);

        return $this->user->map([
            'id' => $userRaw['sub'] ?? null,
            'type' => $userRaw['social_provider'] ?? null,
            'loghyId' => isset($userRaw['loghy_id']) ? (string)$userRaw['loghy_id'] : null,
            'userId' => $userRaw['user_id'] ?? null,
            'name' => $userRaw['name'] ?? null,
            'email' => $userRaw['email'] ?? null,
        ])->setRaw($userRaw);
    }

    /**
     * Set the User instance for the authenticated user.
     *
     * @param \Loghy\SDK\User $user
     *
     * @return $this
     */
    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the user token by exchanging the authorization code.
     * @see https://api-v2-spec.sns-loghy.jp/front.html#tag/auth/operation/post-auth-token
     *
     * @param null|string $code
     *
     * @return array
     * @throws \Loghy\SDK\Exception\LoghyException
     */
    public function token(string $code = null): array
    {
        if ($this->userToken) {
            return $this->userToken;
        }
        $code and $this->setCode($code);

        return $this->requestApi(
            method: 'POST',
            uri: 'auth/token',
            json: [
                'site_code' => $this->siteCode,
                'code' => $this->getCode(),
            ],
        );
    }

    /**
     * Set the user token.
     *
     * @param array $token
     *
     * @return $this
     */
    public function setToken(array $token): static
    {
        $this->userToken = $token;
        return  $this;
    }

    /**
     * Verify ID token and fetch the user profile.
     * @see https://api-v2-spec.sns-loghy.jp/front.html#tag/auth/operation/post-auth-verify
     *
     * @param string $idToken
     *
     * @return array
     * @throws \Loghy\SDK\Exception\LoghyException
     */
    public function verify(string $idToken): array
    {
        return $this->requestApi(
            method: 'POST',
            uri: 'auth/verify',
            json: [
                'id_token' => $idToken,
                'site_code' => $this->siteCode,
            ],
        );
    }

    /**
     * Get social provider tokens.
     * @see https://api-v2-spec.sns-loghy.jp/front.html#operation/get-user-social_provider_token
     *
     * @param null|string $userAccessToken
     *
     * @return array
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\UnsetUserAccessTokenException
     */
    public function socialProviderTokens(string $userAccessToken = null): array
    {
        $userAccessToken ??= $this->userToken['userAccessToken'] ?? throw new UnsetUserAccessTokenException(
            'The access token has not been set. ' .
                'Please call socialProviderToken() method with the access token as an argument.'
        );

        return $this->requestApi(
            method: 'GET',
            uri: 'user/social_provider_token',
            headers: ['Authorization' => 'Bearer ' . $userAccessToken],
        );
    }

    /**
     * Request API
     *
     * @param string $method
     * @param string $uri
     * @param array $json
     * @param array $headers
     *
     * @return array
     * @throws \Loghy\SDK\Exception\LoghyException
     */
    private function requestApi(string $method, string $uri, array $json = [], array $headers = []): array
    {
        try {
            $response = $this->httpClient()->request($method, $uri, [
                'headers' => $headers + ['Accept' => 'application/json'],
                'json' => $json,
            ]);
            return $this->getResponseJson($response);
        } catch (BadResponseException $e) {
            throw new LoghyException(
                $this->getResponseJson($e->getResponse())['message'] ?? '',
                $e->getResponse()->getStatusCode(),
                $e
            );
        } catch (\Exception $e) {
            throw new LoghyException(previous: $e);
        }
    }

    /**
     * Get JSON content from HTTP response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return array
     */
    private function getResponseJson(\Psr\Http\Message\ResponseInterface $response): array
    {
        $body = (string) $response->getBody();
        return json_decode($body, true);
    }

    /**
     * Set the authorization code.
     *
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get the authorization code.
     *
     * @return string
     * @throws \Loghy\SDK\Exception\UnsetCodeException
     */
    public function getCode(): string
    {
        return $this->code ?? throw new UnsetCodeException(
            'The authorization code has not been set. ' .
                'Please call the setCode() method to set up.'
        );
    }

    /**
     * Set the access token for the site required to use management(admin) API.
     *
     * @param string $token
     *
     * @return $this
     */
    public function setSiteAccessToken(string $token): static
    {
        $this->siteAccessToken = $token;
        return $this;
    }

    /**
     * Get the access token for the site required to use management(admin) API.
     *
     * @return string
     * @throws \Loghy\SDK\Exception\UnsetSiteAccessTokenException
     */
    public function getSiteAccessToken(): string
    {
        return $this->siteAccessToken ?? throw new UnsetSiteAccessTokenException(
            'The site access token has not been set. ' .
                'Please call the setSiteAccessToken() method to set up.'
        );
    }

    /**
     * {@inheritdoc}
     * @see https://api-v2-spec.sns-loghy.jp/manage.html#tag/users/operation/put-users-bulk
     *
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\UnsetLoghyIdException
     */
    public function putUserId(string $userId, ?string $loghyId = null): bool
    {
        $loghyId ??= $this->user?->getLoghyId() ?? throw new UnsetLoghyIdException(
            'Loghy ID has not been set. ' .
                'Please call putUserId() method with Loghy ID as an argument.'
        );

        $response = $this->requestApi(
            method: 'PUT',
            uri: 'manage/users/bulk',
            headers: [
                'Authorization' => 'Bearer ' . $this->getSiteAccessToken()
            ],
            json: [
                'users' => [
                    [
                        'loghy_id' => $loghyId,
                        'settings' => [
                            'user_id' => $userId
                        ]
                    ]
                ]
            ],
        );
        return $response['message'] === 'ok';
    }

    /**
     * {@inheritdoc}
     * @see https://api-v2-spec.sns-loghy.jp/manage.html#tag/users/operation/delete-users-bulk
     *
     * @throws \Loghy\SDK\Exception\LoghyException
     * @throws \Loghy\SDK\Exception\UnsetLoghyIdException
     */
    public function deleteUser(string|array $loghyId = null): bool
    {
        $loghyId ??= $this->user?->getLoghyId() ?? throw new UnsetLoghyIdException(
            'Loghy ID has not been set. ' .
                'Please call deleteUser() method with Loghy ID as an argument.'
        );

        $response = $this->requestApi(
            method: 'DELETE',
            uri: 'manage/users/bulk',
            headers: [
                'Authorization' => 'Bearer ' . $this->getSiteAccessToken()
            ],
            json: [
                'loghy_ids' => (array)$loghyId,
            ],
        );
        return $response['message'] === 'ok';
    }

    /**
     * Get API URI.
     *
     * @return string
     */
    protected function getApiUri(): string
    {
        return 'https://api001.sns-loghy.jp/api/v2/';
    }
}
