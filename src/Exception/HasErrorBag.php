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
     * @var \Illuminate\Support\MessageBag
     */
    protected $messageBag;

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
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function getErrors()
    {
        if (is_null($this->messageBag)) {
            $this->messageBag = new MessageBag($this->errorBag);
        }

        return $this->messageBag;
    }
}
