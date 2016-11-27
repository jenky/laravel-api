<?php

namespace Jenky\LaravelAPI\Contracts\Debug;

interface ValidationExceptionInterface
{
    /**
     * Get the error messages.
     *
     * @return mixed
     */
    public function getErrors();

    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors();
}
