<?php

namespace Jenky\LaravelAPI\Exception;

interface ExceptionWithError
{
    /**
     * Get the error messages.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors();

    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors();
}
