<?php

declare(strict_types=1);

namespace PhpJson\Traits;

use PhpJson\Errors\FileException;

trait FileScanner
{
    /** @throws FileException */
    protected static function abortIfExist(string $fileName): void
    {
        if (\file_exists($fileName)) {
            throw new FileException();
        }
    }

    /** @throws FileException */
    protected static function abortIfNotExist(string $fileName): void
    {
        if (!\file_exists($fileName)) {
            throw new FileException();
        }
    }

    protected static function nameFile(string $fileName): string
    {
        $t = \explode('.', $fileName);
        if (\end($t) == 'json') {
            return $fileName;
        }
        return "{$fileName}.json";
    }

    protected static function prepareMakeFile(string $fileName): string
    {
        $fileName = static::nameFile($fileName);
        static::abortIfExist($fileName);

        \touch($fileName);
        return $fileName;
    }

    protected static function prepareGetFile(string $fileName): string
    {
        $fileName = static::nameFile($fileName);
        static::abortIfNotExist($fileName);

        return $fileName;
    }
}
