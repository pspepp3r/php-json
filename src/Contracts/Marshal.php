<?php

declare(strict_types=1);

namespace PhpJson\Contracts;

interface Marshal
{
    /** Creates a JSON file. */
    public static function make(string $fileName): JsonContent;

    /** Alters the contents of a JSON file. */
    public static function alter(string $fileName): JsonContent;

    /** Reads the content of the file to PHP Object */
    public static function read(string $fileName): JsonContent;
}
