<?php

declare(strict_types=1);

namespace Loghy\SDK;

use ArrayAccess;
use Loghy\SDK\Contract\User as ContractUser;

class User implements ContractUser, ArrayAccess
{
    /**
     * The unique identifier for the user.
     */
    public string $id;

    /**
     * The type of social provider.
     */
    public string $type;

    /**
     * The unique identifier for the user issued by Loghy.
     */
    public string $loghyId;

    /**
     * The identifier the user issued by the sites.
     */
    public string $userId;

    /**
     * The user's name.
     */
    public string $name;

    /**
     * The user's e-mail address.
     */
    public string $email;

    /**
     * The user's raw attributes.
     */
    public array $user;

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoghyId(): string
    {
        return $this->loghyId;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Get the raw user array.
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->user;
    }

    /**
     * Set the raw user array from the provider.
     *
     * @param  array  $user
     * @return $this
     */
    public function setRaw(array $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Map the given array onto the user's properties.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function map(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Determine if the given raw user attribute exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->user);
    }

    /**
     * Get the given key from the raw user.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->user[$offset];
    }

    /**
     * Set the given attribute on the raw user array.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->user[$offset] = $value;
    }

    /**
     * Unset the given value from the raw user array.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->user[$offset]);
    }
}
