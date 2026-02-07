<?php

declare(strict_types=1);

namespace PhpJson\Marshals;

use PhpJson\Contents\ObjectContent;
use PhpJson\Contracts\{Marshal};
use PhpJson\Traits\FileScanner;

final class ObjectMarshal implements Marshal
{
    use FileScanner;

    public static function make(string $fileName): ObjectContent
    {
        $fileName = static::prepareMakeFile($fileName);

        return new ObjectContent($fileName);
    }

    public static function read(string $fileName): ObjectContent
    {
        $fileName = static::prepareGetFile($fileName);

        return new ObjectContent($fileName);
    }

    public static function alter(string $fileName): ObjectContent
    {
        $fileName = static::prepareGetFile($fileName);

        return new ObjectContent($fileName);
    }
}
