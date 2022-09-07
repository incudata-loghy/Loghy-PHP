<?php

declare(strict_types=1);

namespace Loghy\SDK\Contract;

/**
 * Interface LoghyInterface
 */
interface LoghyInterface
{
    /**
     * Get the User instance for the authenticated user.
     * 
     * @param null|string $code
     *
     * @return \Loghy\SDK\Contract\User
     */
    public function user(string $code = null): User;

    /**
     * Set user ID by site to a Loghy ID.
     *
     * @param string $userId
     * @param string|null $loghyId
     * @return bool
     */
    public function putUserId(string $userId, string $loghyId = null): bool;

    /**
     * Delete user (Loghy ID).
     *
     * @param string|null $loghyId
     * @return bool
     */
    public function deleteUser(string $loghyId = null): bool;
}
