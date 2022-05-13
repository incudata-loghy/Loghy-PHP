<?php

declare(strict_types=1);

namespace Loghy\SDK\Contract;

/**
 * Interface User
 */
interface User
{
    /**
     * Get the unique identifier for the user issued by social providers.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get the unique identifier for the user issued by Loghy.
     * 
     * @return string
     */
    public function getLoghyId(): string;

    /**
     * Get the identifier the user issued by the sites.
     * 
     * @return string
     */
    public function getUserId(): ?string;

    /**
     * Get the name of the user.
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Get the e-mail address of the user.
     *
     * @return string
     */
    public function getEmail(): ?string;
}
