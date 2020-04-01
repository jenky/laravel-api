<?php

namespace Jenky\LaravelAPI\Contracts\Exception;

interface ErrorException extends \Throwable
{
    /**
     * Get the error messages.
     *
     * @return array
     */
    public function getErrors(): array;
}
