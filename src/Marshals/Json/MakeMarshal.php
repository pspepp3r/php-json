<?php

declare(strict_types=1);

namespace PhpJson\Marshals\Json;

class MakeMarshal extends BaseMarshal
{
    public function addValue(string|int $nameOrCollectionValue, $value): static
    {
        $this->secretAddValue($value, $nameOrCollectionValue);

        return $this;
    }

    public function parseFrom(object $dataObject): void
    {
        if (!$this->content) {
            $this->content = $dataObject;
            $this->store();
            return;
        }

        $this->reset();
    }

    protected function secretAddValue(mixed $value, string|int|null $name = null): void
    {
        if ($name) {
            $this->prepareObjectContent($name, $value);
        } else {
            $this->prepareCollectionContent();
        }
    }

    protected function reset(): void
    {
        unset($this->content);
        \unlink($this->filename);
    }

    private function prepareObjectContent($name, $value): void
    {
        if (!$this->content) $this->content = new \stdClass();


        if (!\property_exists($this->content, $name)) {
            $this->content->$name = $value;
        } else {
            $this->reset();
            throw new \RuntimeException("This property is already set. Please check your pipe!");
        }
    }

    private function prepareCollectionContent(): void
    {
        if (!$this->content) $this->content = [];
    }
}
