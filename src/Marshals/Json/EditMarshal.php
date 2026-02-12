<?php

declare(strict_types=1);

namespace PhpJson\Marshals\Json;

class EditMarshal extends BaseMarshal
{
    public function setValue(string|int $nameOrCollectionValue, $value)
    {
        $this->secretSetValue($value, $nameOrCollectionValue);

        return $this;
    }

    public function appendValue(string|int $nameOrCollectionValue, $value): static
    {
        $this->secretAppendValue($value, $nameOrCollectionValue);

        return $this;
    }

    public function removeValue(string|int $key)
    {
        if (!$this->content) $this->content = \json_decode(\file_get_contents($this->filename));

        // support "base[index]" notation
        if (\is_string($key) && \preg_match('/^([^\[\]]+)\[(.+)\]$/', $key, $m)) {
            $base = $m[1];
            $inner = $m[2];

            // object top-level
            if (\is_object($this->content) && \property_exists($this->content, $base)) {
                $container = $this->content->$base;
                if (\is_array($container) && \array_key_exists($inner, $container)) {
                    unset($this->content->$base[$inner]);
                    return $this;
                }
                if (\is_object($container) && \property_exists($container, $inner)) {
                    unset($this->content->$base->$inner);
                    return $this;
                }
            }

            // array top-level
            if (\is_array($this->content) && \array_key_exists($base, $this->content)) {
                $container = $this->content[$base];
                if (\is_array($container) && \array_key_exists($inner, $container)) {
                    unset($this->content[$base][$inner]);
                    return $this;
                }
                if (\is_object($container) && \property_exists($container, $inner)) {
                    unset($this->content[$base]->$inner);
                    return $this;
                }
            }

            $this->reset();
            throw new \RuntimeException("This value does not exist.");
        }

        // simple key removal
        if (\is_object($this->content)) {
            if (\property_exists($this->content, (string) $key)) {
                unset($this->content->{(string) $key});
                return $this;
            }
        } elseif (\is_array($this->content)) {
            if (\array_key_exists($key, $this->content)) {
                unset($this->content[$key]);
                return $this;
            }
        }

        $this->reset();
        throw new \RuntimeException("This value does not exist.");
    }

    protected function reset(): void
    {
        unset($this->content);
    }

    private function secretSetValue(mixed $value, string|int|null $name = null)
    {
        if ($name) {
            $this->prepareObjectContent($name, $value);
        } else {
            $this->prepareCollectionContent($value);
        }
    }

    private function secretAppendValue(mixed $value, string|int|null $name = null)
    {
        if ($name) {
            $this->appendObjectContent($name, $value);
        } else {
            $this->appendCollectionContent($value);
        }
    }

    private function prepareObjectContent(string|int $name, mixed $value)
    {
        if (!$this->content) $this->content = (object) \json_decode(\file_get_contents($this->filename));

        if (\property_exists($this->content, $name)) {
            $this->content->$name = $value;
        } else {
            $this->reset();
            throw new \RuntimeException("This property is not set.");
        }
    }

    private function prepareCollectionContent(mixed $value)
    {
        if (!$this->content) $this->content = (array) \json_decode(\file_get_contents($this->filename));
        if ($key = \array_search($value, $this->content)) {
            $this->content[$key] = $value;
        } else {
            $this->reset();
            throw new \RuntimeException("This value does not exist.");
        }
    }

    private function appendObjectContent(string|int $name, mixed $value)
    {
        if (!$this->content) $this->content = (object) \json_decode(\file_get_contents($this->filename));

        if (\property_exists($this->content, $name)) {
            $this->reset();
            throw new \RuntimeException("This overrides the property $name.");
        } else {
            $this->content->$name = $value;
        }
    }

    private function appendCollectionContent(mixed $value)
    {
        if (!$this->content) $this->content = [];

        $this->content[] = $value;
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

        throw new \RuntimeException('The DataObject and this JSONObject don\'t match');
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
}
