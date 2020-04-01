<?php

namespace Jenky\LaravelAPI\Exception;

interface ExceptionWithErrors extends \Throwable
{
    /**
     * Get the error messages.
     *
     * @return array
     */
    public function getErrors(): array;
}
