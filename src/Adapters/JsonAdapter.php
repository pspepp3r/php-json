<?php

declare(strict_types=1);

namespace PhpJson\Adapters;

use PhpJson\Marshals\Json\{EditMarshal, MakeMarshal};
use PhpJson\Marshals\Json\BaseMarshal as Marshal;

class JsonAdapter implements \PhpJson\Contracts\Adapter
{
    public static function getMarshall(string $filename): Marshal
    {
        $filename = self::nameFile($filename);

        if (\file_exists($filename)) {
            return new EditMarshal($filename);
        }

        \touch($filename);
        return new MakeMarshal($filename);
    }

    protected static function nameFile(string $fileName): string
    {
        $t = \explode('.', $fileName);
        if (\end($t) == 'json') {
            return $fileName;
        }
        return "{$fileName}.json";
    }
}
