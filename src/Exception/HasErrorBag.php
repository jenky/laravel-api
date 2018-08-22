<?php

namespace Jenky\LaravelAPI\Exception;

use Illuminate\Support\MessageBag;

trait HasErrorBag
{
    /**
     * @var array
     */
    protected $errorBag = [];

    /**
     * Set the error messages.
     *
     * @param  array $errors
     * @return $this
     */
    public function setErrors(array $errors)
    {
        $this->errorBag = $errors;

        return $this;
    }

    /**
     * Get the error messages.
     *
     * @param  array $errors
     * @return mixed
     */
    public function getErrors()
    {
        return new MessageBag($this->errorBag);
    }
}
