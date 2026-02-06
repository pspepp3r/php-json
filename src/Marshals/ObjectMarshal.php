<?php

declare(strict_types=1);

namespace PhpJson\Marshals;

use PhpJson\Contents\ObjectContent;
use PhpJson\Contracts\{Marshal};
use PhpJson\Errors\FileException;

final class ObjectMarshal implements Marshal
{
    public static function make(string $fileName): ObjectContent
    {
        $fileName = static::nameFile($fileName);
        static::abortIfExist($fileName);

        \touch($fileName);

        return new ObjectContent($fileName);
    }

    public static function makeSafe(string $fileName): ObjectContent
    {
        static::abortIfExist($fileName);
        return new ObjectContent($fileName);
    }

    public function read(string $fileName): ObjectContent
    {
        return new ObjectContent($fileName);
    }

    public static function alter(string $fileName): ObjectContent
    {
        return new ObjectContent($fileName);
    }

    public static function alterSafe(string $fileName): ObjectContent
    {
        return new ObjectContent($fileName);
    }

    /** @throws FileException */
    private static function abortIfExist(string $fileName)
    {
        if (\file_exists($fileName)) {
            throw new FileException();
        }
    }

    private static function nameFile(string $fileName): string
    {
        $t = \explode('.', $fileName);
        if (\end($t) == 'json') {
            return $fileName;
        }
        return "{$fileName}.json";
    }
}
