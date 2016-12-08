<?php

namespace Jenky\LaravelAPI\Exception;

use Exception;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErrorException extends HttpException implements ExceptionWithError
{
    /**
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Create a new exception handler.
     *
     * @param  array $errors
     * @param  string|null $message
     * @param  int $statusCode
     * @param  \Exception|null $previous
     * @param  array $headers
     * @param  int $code
     * @return void
     */
    public function __construct(array $errors = [], $message = null, $statusCode = 422, Exception $previous = null, array $headers = [], $code = 0)
    {
        $this->errors = new MessageBag($errors);

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * Get the error messages.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return ! $this->errors->isEmpty();
    }
}
