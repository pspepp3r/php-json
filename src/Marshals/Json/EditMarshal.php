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

        // support "base[index][inner]..." notation
        if (\is_string($key) && \preg_match_all('/([^\[\]]+)/', $key, $m)) {
            $segments = $m[1];
            $last = \array_pop($segments);

            $current = &$this->content;

            foreach ($segments as $seg) {
                if (\is_object($current)) {
                    if (\property_exists($current, $seg)) {
                        $current = &$current->$seg;
                    } else {
                        $this->reset();
                        throw new \RuntimeException("This value does not exist.");
                    }
                } elseif (\is_array($current)) {
                    if (\array_key_exists($seg, $current)) {
                        $current = &$current[$seg];
                    } else {
                        $this->reset();
                        throw new \RuntimeException("This value does not exist.");
                    }
                } else {
                    $this->reset();
                    throw new \RuntimeException("This value does not exist.");
                }
            }

            if (\is_object($current) && \property_exists($current, $last)) {
                unset($current->$last);
                return $this;
            }

            if (\is_array($current) && \array_key_exists($last, $current)) {
                unset($current[$last]);
                return $this;
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

    public function readMode(): ReadMarshal
    {
        return new ReadMarshal($this->filename);
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
}
