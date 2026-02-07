<?php

declare(strict_types=1);

namespace PhpJson\Contracts;

interface JsonContent
{
    public function addProperty(string $name, mixed $value): static;

    public function parseObject(object $dataObject): void;

    public function setProperty(string $name, mixed $value): static;

    public function store(): void;
}
