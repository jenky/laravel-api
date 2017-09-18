<?php

namespace Jenky\LaravelAPI\Exception;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Foundation\Exceptions\Handler as IlluminateExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends IlluminateExceptionHandler implements ExceptionHandler, ExceptionHandlerContract
{
    /**
     * User defined replacements to merge with defaults.
     *
     * @var array
     */
    protected $replacements = [];

    /**
     * Indicates that error trace should be string instead of array.
     *
     * @var bool
     */
    protected static $getTraceAsString = false;

    /**
     * Disable wrapping of the outer-most resource array.
     *
     * @return void
     */
    public static function getTraceAsString($bool = true)
    {
        static::$getTraceAsString = $bool;
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
        return $this->toResponse($e, 401);
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
        if ($e->response) {
            return $e->response;
        }

        return $this->toResponse($e, $e->status);
    }

    /**
     * Prepare a response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareResponse($request, Exception $e)
    {
        return $this->addCorsHeaders($this->toResponse($e));
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Exception $e)
    {
        return $this->addCorsHeaders($this->toResponse($e));
    }

    /**
     * Add cors to response headers.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Illuminate\Http\Request $request
     */
    protected function addCorsHeaders($response, $request)
    {
        if ($this->container->bound(\Barryvdh\Cors\Stack\CorsService::class)) {
            $response = $this->container[\Barryvdh\Cors\Stack\CorsService::class]->addActualRequestHeaders($response, $request);
        }

        return $response;
    }

    /**
     * Map exception into an JSON response.
     *
     * @param  \Exception $e
     * @param  null|int $statusCode
     * @param  array $headers
     * @return \Illuminate\Http\Response
     */
    protected function toResponse(Exception $exception, $statusCode = null, array $headers = [])
    {
        $replacements = $this->prepareReplacements($exception, $statusCode, $headers);
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
     * @param  \Exception $exception
     * @param  null|int $statusCode
     * @param  array $headers
     * @return array
     */
    protected function prepareReplacements(Exception &$exception, $statusCode = null, array $headers = [])
    {
        $e = FlattenException::create($exception, $statusCode, $headers);
        $replacements = [];
        $statusCode = $e->getStatusCode();

        if (! $message = $e->getMessage()) {
            $message = array_get(Response::$statusTexts, $statusCode);
        }

        if ($exception instanceof ValidationException) {
            $validator = $exception->validator;

            if (! $validator->errors()->isEmpty()) {
                $replacements[':errors'] = $validator->errors();
            }
        }

        $replacements += [
            ':message' => $message,
            ':status_code' => $statusCode,
            ':type' => class_basename($e->getClass()),
        ];

        if ($code = $e->getCode()) {
            $replacements[':code'] = $code;
        }

        if ($this->runningInDebugMode()) {
            $replacements[':debug'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'class' => $e->getClass(),
                'trace' => static::$getTraceAsString ? explode("\n", $exception->getTraceAsString()) : $e->getTrace(),
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
        return $this->container['config']->get('api.errorFormat', [
            'message' => ':message',
            'type' => ':type',
            'status_code' => ':status_code',
            'errors' => ':errors',
            'code' => ':code',
            'debug' => ':debug',
        ]);
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
