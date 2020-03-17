<?php

namespace Jenky\LaravelAPI\Exception;

trait HasErrorBag
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Set the error messages.
     *
     * @param  array $errors
     * @return $this
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get the error messages.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
