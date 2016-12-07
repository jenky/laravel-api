<?php

namespace Jenky\LaravelAPI\Exception;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Foundation\Exceptions\Handler as IlluminateExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler;
use ReflectionFunction;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends IlluminateExceptionHandler implements ExceptionHandler, ExceptionHandlerContract
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Array of exception handlers.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * User defined replacements to merge with defaults.
     *
     * @var array
     */
    protected $replacements = [];

    /**
     * Prepare exception for rendering.
     *
     * @param  \Exception  $e
     * @return \Exception
     */
    protected function prepareException(Exception $e)
    {
        $e = parent::prepareException($e);

        if ($e instanceof AuthenticationException) {
            $e = new HttpException(401, $e->getMessage());
        }

        foreach ($this->handlers as $hint => $handler) {
            if (! $e instanceof $hint) {
                continue;
            }

            if ($response = $handler($e, $this)) {
                if ($response instanceof Response) {
                    return $response;
                }
            }
        }

        return $e;
    }

    /**
     * Prepare response containing exception render.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    protected function prepareResponse($request, Exception $exception)
    {
        return $this->toResponse($exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $e
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $e)
    {
        return $this->toResponse($e);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException $e
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        return $this->toResponse($e);
    }

    /**
     * Map exception into an JSON response.
     *
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    protected function toResponse(Exception $exception)
    {
        $replacements = $this->prepareReplacements($exception);
        $response = $this->getErrorFormat();

        array_walk_recursive($response, function (&$value, $key) use ($exception, $replacements) {
            if (starts_with($value, ':') && isset($replacements[$value])) {
                $value = $replacements[$value];
            }
        });

        $response = $this->removeEmptyReplacements($response);

        return new JsonResponse($response, $exception->getStatusCode(), $exception->getHeaders());
    }

    /**
     * Prepare the replacements array by gathering the keys and values.
     *
     * @param \Exception $exception
     * @return array
     */
    protected function prepareReplacements(Exception &$exception)
    {
        $e = FlattenException::create($exception);
        $replacements = [];
        $statusCode = $e->getStatusCode();

        if (! $message = $e->getMessage()) {
            $message = array_get(Response::$statusTexts, $statusCode);
        }

        if ($exception instanceof ValidationException) {
            $validator = $exception->validator;
            $statusCode = 422;
            $e->setStatusCode($statusCode);

            if (! $validator->errors()->isEmpty()) {
                $replacements[':errors'] = $validator->errors();
            }
        }

        $replacements += [
            ':message' => $message,
            ':status_code' => $statusCode,
        ];

        if ($code = $e->getCode()) {
            $replacements[':code'] = $code;
        }

        if ($this->runningInDebugMode()) {
            $replacements[':debug'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'class' => $e->getClass(),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
        }

        $exception = $e;

        return array_merge($replacements, $this->replacements);
    }

    /**
     * Recursirvely remove any empty replacement values in the response array.
     *
     * @param array $input
     * @return array
     */
    protected function removeEmptyReplacements(array $input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->removeEmptyReplacements($value);
            }
        }

        return array_filter($input, function ($value) {
            if (is_string($value)) {
                return ! starts_with($value, ':');
            }

            return true;
        });
    }

    /**
     * Register a new exception handler.
     *
     * @param  callable $callback
     * @return void
     */
    public function register(callable $callback)
    {
        $hint = $this->handlerHint($callback);

        $this->handlers[$hint] = $callback;
    }

    /**
     * Get the hint for an exception handler.
     *
     * @param callable $callback
     *
     * @return string
     */
    protected function handlerHint(callable $callback)
    {
        $reflection = new ReflectionFunction($callback);

        $exception = $reflection->getParameters()[0];

        return $exception->getClass()->getName();
    }

    /**
     * Determines if we are running in debug mode.
     *
     * @return bool
     */
    protected function runningInDebugMode()
    {
        return $this->container['config']->get('app.debug', false);
    }

    /**
     * Get error format.
     *
     * @return array
     */
    protected function getErrorFormat()
    {
        return [
            'message' => ':message',
            'status_code' => ':status_code',
            'errors' => ':errors',
            'code' => ':code',
            'debug' => ':debug',
        ];
    }

    /**
     * Set the error format array.
     *
     * @param  array $format
     * @return $this
     */
    public function setErrorFormat(array $format)
    {
        // $this->format = $format;

        return $this;
    }

    /**
     * Set user defined replacements.
     *
     * @param  array $replacements
     * @return $this
     */
    public function setReplacements(array $replacements)
    {
        $this->replacements = $replacements;

        return $this;
    }
}
