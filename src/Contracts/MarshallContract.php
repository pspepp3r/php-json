<?php

declare(strict_types=1);

namespace PhpJson\Contracts;

interface MarshallContract
{
    /**
     * Creates a JSON file. The file extension isn't compulsory.
     * 
     * @param string $fileName
     * 
     * @return void
     */
    public static function make(string $fileName);

    /**
     * Creates a JSON file. The file extension isn't compulsory.
     * 
     * @param string $fileName
     * 
     * @return void
     */
    public static function makeSafe(string $fileName);

    public static function alter();

    public function read();
}
