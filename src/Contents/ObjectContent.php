<?php

declare(strict_types=1);

namespace PhpJson\Contents;

use PhpJson\Errors\FileException;
use PhpJson\Contracts\JsonContent;
use ReflectionClass;
use function is_object;

final class ObjectContent implements JsonContent
{
    private mixed $content = \null;

    public function __construct(
        public readonly string $fileName,
        public readonly ?bool $flag = false
    ) {}

    public function addProperty(string $name, mixed $value): static
    {
        $this->prepareNewContent();

        if (!\property_exists($this->content, $name)) {
            $this->content->$name = $value;
            return $this;
        }
        \unlink($this->fileName);
        throw new \RuntimeException("Cannot override property, use setPropety() instead");
    }

    public function setProperty(string $name, mixed $value): static
    {
        $this->prepareObjectContent();

        if (\property_exists($this->content, $name)) {
            $this->content->$name = $value;
            return $this;
        } else
            throw new \RuntimeException("Property $name doesn't exist in this object. Use addProperty() instead");
    }

    public function appendProperty(string $name, mixed $value)
    {

        $this->prepareObjectContent();


        if (!\property_exists($this->content, $name)) {
            $this->content->$name = $value;
            return $this;
        }
        throw new \RuntimeException("Cannot override already set property, setting $name. Use setProperty() instead");
    }

    public function parseObject(object $dataObject): void
    {
        $this->content = $dataObject;
        $this->store();
    }

    /** @param class-string $class */
    public function toDataObject(string $class)
    {
        $this->prepareObjectContent();

        $reflectionClass = new ReflectionClass($class);

        if (array_intersect(
            array_map(
                fn($parameter) =>  $parameter->name,
                $reflectionClass->getConstructor()->getParameters()
            ),
            array_keys((array) $this->content)
        ))
            return new $class(...$this->recursivelyPopulate((array) $this->content, $reflectionClass));

        throw new \RuntimeException('The DataObject and this JSONObject don\'t match');
    }

    private function recursivelyPopulate(array $data, ReflectionClass $reflectionClass): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $valueClass = $reflectionClass->getProperty($key)?->getType()?->getName();
                $result[$key] = ($valueClass && class_exists($valueClass)) ? new $valueClass(...$this->recursivelyPopulate((array) $value, new ReflectionClass($valueClass))) : $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private function prepareNewContent()
    {
        if (!$this->content) {
            $this->content = new \stdClass();
        }
    }

    private function prepareObjectContent()
    {
        if (!$this->content) {
            $this->content = \json_decode(
                \file_get_contents($this->fileName),
                flags: JSON_BIGINT_AS_STRING |
                    JSON_INVALID_UTF8_IGNORE |
                    JSON_INVALID_UTF8_SUBSTITUTE |
                    JSON_THROW_ON_ERROR
            );
        }
    }


    public function store(): void
    {
        try {
            $content = \json_encode($this->content, \JSON_FORCE_OBJECT |
                \JSON_HEX_QUOT |
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
            if (!\file_put_contents($this->fileName, $content))
                throw new FileException();
        } catch (\JsonException | FileException) {
        } finally {
            unset($this->content);
        }
    }
}
