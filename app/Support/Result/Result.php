<?php

namespace App\Support\Result;

readonly class Result
{
    private function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $error = null
    ) {}

    public static function success(mixed $data = null): self
    {
        return new self(true, $data);
    }

    public static function failure(string $error): self
    {
        return new self(false, null, $error);
    }

    public function failed(): bool
    {
        return !$this->success;
    }
}
