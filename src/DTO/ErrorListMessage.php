<?php declare(strict_types=1);

namespace Murdej\DTO;

class ErrorListMessage
{
    public function __construct(
        public string $field,
        public string $message,
    ) { }
}