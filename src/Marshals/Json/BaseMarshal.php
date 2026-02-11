<?php

declare(strict_types=1);

namespace PhpJson\Marshals\Json;

use PhpJson\Errors\FileException;
use PhpJson\Marshals\BaseMarshal as Marshal;

abstract class BaseMarshal extends Marshal
{
    protected mixed $content = \null;

    public function __construct(protected readonly string $filename) {}

    public function store(): void
    {
        if ($this->content) {
            try {
                $content = \json_encode($this->prepareData($this->content), \JSON_HEX_QUOT |
                    \JSON_HEX_TAG |
                    \JSON_HEX_AMP |
                    \JSON_HEX_APOS |
                    \JSON_INVALID_UTF8_IGNORE |
                    \JSON_INVALID_UTF8_SUBSTITUTE |
                    \JSON_NUMERIC_CHECK |
                    \JSON_PARTIAL_OUTPUT_ON_ERROR |
                    \JSON_PRESERVE_ZERO_FRACTION |
                    \JSON_PRETTY_PRINT |
                    \JSON_UNESCAPED_LINE_TERMINATORS |
                    \JSON_UNESCAPED_SLASHES |
                    \JSON_UNESCAPED_UNICODE |
                    \JSON_THROW_ON_ERROR);

                if (!\file_put_contents($this->filename, $content))
                    throw new FileException();
            } catch (\JsonException | FileException) {
            } finally {
                unset($this->content);
            }
        } else {
            echo "Nothing to change";
        }
    }

    abstract protected function reset(): void;

    private function prepareData(mixed $data)
    {
        if (\is_array($data)) {
            return \array_map($this->prepareData(...), $data);
        }

        if (\is_object($data)) {
            if (
                $data instanceof \ArrayAccess
                || $data instanceof \IteratorAggregate
                || $data instanceof \Traversable
            ) {
                return $this->prepareData((array) $data);
            }

            return (object) \array_map($this->prepareData(...), (array) $data);
        }

        return $data;
    }
}
