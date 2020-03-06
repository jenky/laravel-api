<?php

namespace Jenky\LaravelAPI\Exception;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Throwable;

trait ExceptionResponse
{
    use FormatsException;

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $e
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $e)
    {
        return $this->expectsJson($request)
            ? $this->toJsonResponse($e, 401)
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
        if ($e->response) {
            return $e->response;
        }

        return $this->expectsJson($request)
            ? $this->toJsonResponse($e, $e->status)
            : parent::convertValidationExceptionToResponse($e, $request);
    }

    /**
     * Prepare a response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareResponse($request, Throwable $e)
    {
        return $this->expectsJson($request)
            ? $this->toJsonResponse($e)
            : parent::prepareResponse($request, $e);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        return $this->expectsJson($request)
            ? $this->toJsonResponse($e)
            : parent::prepareJsonResponse($request, $e);
    }

    /**
     * Determine whether current URI is an API route.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function expectsJson(Request $request)
    {
        return $this->container[Validator::class]->matches($request);
    }
}
