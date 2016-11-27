<?php

namespace Jenky\LaravelAPI\Exception;

use Exception;
use Illuminate\Validation\ValidationException;
use Jenky\LaravelAPI\Contracts\Debug\ValidationExceptionInterface;

class ValidationExceptionHandler extends Exception implements ValidationExceptionInterface
{
    /**
     * @var \Illuminate\Validation\ValidationException
     */
    protected $exception;

    /**
     * @var string
     */
    protected $message;

    /**
     * Create a new validation exception handler class.
     *
     * @param  \Illuminate\Validation\ValidationException $exception
     * @return void
     */
    public function __construct(ValidationException $exception)
    {
        $this->exception = $exception;
        $this->message = $exception->getMessage();
    }

    /**
     * Get the error messages.
     *
     * @return array
     */
    public function getErrors()
    {
        $output = [];
        $errors = $this->exception->validator->errors();

        foreach ($errors->messages() as $field => $message) {
            $output[] = [
                'field' => $field,
                'message' => isset($message[0]) ? $message[0] : '',
            ];
        }

        return $output;
    }

    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return ! $this->exception->validator->errors()->isEmpty();
    }
}
