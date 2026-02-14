<?php

declare(strict_types=1);

namespace PhpJson\Marshals\Json;

class ReadMarshal extends BaseMarshal
{
    public function __construct(string $filename)
    {
        parent::__construct($filename);
        $this->content = \json_decode(\file_get_contents($this->filename));
    }

    public function toArray(): array
    {
        return (array) $this->content;
    }

    public function toObject(): object
    {
        return (object) $this->content;
    }

    public function toJson(): string
    {
        return \json_encode($this->content);
    }

    public function toCollection(): \stdClass
    {
        return $this->content;
    }

    /** @param class-string $class */
    public function toDataObject(string $class)
    {
        $reflectionClass = new \ReflectionClass($class);

        if (\array_intersect(
            \array_map(
                fn($parameter) =>  $parameter->name,
                $reflectionClass->getConstructor()->getParameters()
            ),
            \array_keys((array) $this->content)
        ))
            return new $class(...$this->recursivelyPopulate((array) $this->content, $reflectionClass));

        throw new \RuntimeException('The Data Object and this JSON Object don\'t match');
    }

    private function recursivelyPopulate(array $data, \ReflectionClass $reflectionClass): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (\is_object($value)) {
                $valueClass = (string) $reflectionClass->getProperty($key)?->getType();
                $result[$key] = ($valueClass && \class_exists($valueClass)) ?
                    new $valueClass(...$this->recursivelyPopulate(
                        (array) $value,
                        new \ReflectionClass($valueClass)
                    )) : $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    protected function reset(): void {}
}
