<?php

namespace Jenky\LaravelAPI\Exception;

interface ExceptionWithErrors
{
    /**
     * Get the error messages.
     *
     * @return array
     */
    public function getErrors(): array;
}
