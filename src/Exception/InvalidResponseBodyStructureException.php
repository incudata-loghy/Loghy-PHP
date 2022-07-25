<?php

namespace Loghy\SDK\Exception;

class InvalidResponseBodyStructureException extends LoghyException
{
    public function __construct(string $message, private array $response)
    {
        parent::__construct($message);
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}
