<?php

declare(strict_types=1);

namespace Loghy\SDK\Contract;

/**
 * Interface LoghyInterface
 */
interface LoghyInterface
{
    /**
     * Set the authorization code.
     *
     * @return $this
     */
    public function setCode(string $code): static;

    /**
     * Get the User instance for the authenticated user.
     *
     * @return \Loghy\SDK\Contract\User
     */
    public function user(): User;

    /**
     * Set user ID by site to a Loghy ID
     *
     * @param string $userId
     * @param string|null $loghyId
     * @return bool
     */
    public function putUserId(string $userId, string $loghyId = null): bool;

    /**
     * Delete Loghy ID
     *
     * @param string|null $loghyId
     * @return bool
     */
    public function deleteLoghyId(string $loghyId = null): bool;
}
