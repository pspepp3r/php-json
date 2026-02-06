<?php

declare(strict_types=1);

namespace PhpJson\Contents;

use PhpJson\Errors\FileException;
use PhpJson\Contracts\JsonContent;

class ObjectContent implements JsonContent
{
    private $content;

    public function __construct(
        public readonly string $fileName,
        public readonly ?bool $flag = false
    ) {}

    public function addProperty(string $name, mixed $value): static
    {
        if (!$this->content)
            $this->content = new \stdClass();

        $this->content->$name = $value;
        return $this;
    }

    public function store(): void
    {
        try {
            $content = \json_encode($this->content, JSON_FORCE_OBJECT |
                JSON_HEX_QUOT |
                JSON_HEX_TAG |
                JSON_HEX_AMP |
                JSON_HEX_APOS |
                JSON_INVALID_UTF8_IGNORE |
                JSON_INVALID_UTF8_SUBSTITUTE |
                JSON_NUMERIC_CHECK |
                JSON_PARTIAL_OUTPUT_ON_ERROR |
                JSON_PRESERVE_ZERO_FRACTION |
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_LINE_TERMINATORS |
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE |
                JSON_THROW_ON_ERROR);
            if (!\file_put_contents($this->fileName, $content))
                throw new FileException();
        } catch (\JsonException | FileException) {
        }
    }
}
