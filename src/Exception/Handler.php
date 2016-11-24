<?php

namespace Jenky\LaravelAPI\Exception;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Foundation\Exceptions\Handler as IlluminateExceptionHandler;
use Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends IlluminateExceptionHandler implements ExceptionHandler, ExceptionHandlerContract
{
    /**
     * Prepare response containing exception render.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    protected function prepareResponse($request, Exception $exception)
    {
        $e = FlattenException::create($exception);
        if ($this->isHttpException($exception)) {
            $e->setMessage(array_get(Response::$statusTexts, $exception->getStatusCode()));
        }

        return response()->json([
            'message' => $e->getMessage(),
            'status_code' => $e->getStatusCode(),
            // 'debug' => $e->getTrace(),
            'debug' => explode("\n", $exception->getTraceAsString()),
        ], $e->getStatusCode(), $e->getHeaders());
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
        return response()->json([
            'message' => $e->getMessage(),
            'status_code' => 401,
            'debug' => $e->getTrace(),
        ], 401);
    }
}
