<?php

namespace Jenky\LaravelAPI\Http\Concerns;

trait InteractsWithPath
{
    /**
     * Explode path to an array.
     *
     * @param  string $path
     * @param  string $delimiter
     * @return array
     */
    public function explode(string $path, string $delimiter = '/'): array
    {
        return array_filter(explode($delimiter, $path));
    }
}
