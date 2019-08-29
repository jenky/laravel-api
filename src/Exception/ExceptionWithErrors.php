<?php

namespace Jenky\LaravelAPI\Exception;

interface ExceptionWithErrors
{
    /**
     * Set the error messages.
     *
     * @param  array $errors
     * @return mixed
     */
    public function setErrors(array $errors);

    /**
     * Get the error messages.
     *
     * @return mixed
     */
    public function getErrors();
}
