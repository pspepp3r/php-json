<?php

declare(strict_types=1);

namespace PhpJson\Contracts;

use PhpJson\Marshals\BaseMarshal as Marshal;

interface Adapter
{
    public static function getMarshall(string $filename): Marshal;
}
