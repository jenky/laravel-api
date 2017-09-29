<?php

namespace Jenky\LaravelAPI\Exception;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Symfony\Component\HttpFoundation\Response;

trait ExceptionResponse
{
    use FormatsException;

    /**
     * @var bool
     */
    protected static $isApiRequest;

    /**
     * Check if request is from API.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function isApiRequest($request)
    {
        if (is_null(static::$isApiRequest)) {
            static::$isApiRequest = $this->container[Validator::class]->validate($request);
        }

        return static::$isApiRequest;
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
        return $this->isApiRequest($request)
            ? $this->addCorsHeaders($this->toJsonResponse($e, 401), $request)
            : parent::unauthenticated($request, $e);
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
        return $this->isApiRequest($request)
            ? $this->addCorsHeaders($this->toJsonResponse($e, $e->status), $request)
            : parent::convertValidationExceptionToResponse($e, $request);
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
        return $this->isApiRequest($request)
            ? $this->addCorsHeaders($this->toJsonResponse($e), $request)
            : parent::prepareResponse($request, $e);
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
        return $this->isApiRequest($request)
            ? $this->addCorsHeaders($this->toJsonResponse($e), $request)
            : parent::prepareJsonResponse($request, $e);
    }

    /**
     * Add cors to response headers.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Illuminate\Http\Request $request
     */
    public function addCorsHeaders($response, $request)
    {
        if ($this->container->bound(\Barryvdh\Cors\CorsService::class)) {
            $response = $this->container[\Barryvdh\Cors\CorsService::class]->addActualRequestHeaders($response, $request);
        }

        return $response;
    }
}
