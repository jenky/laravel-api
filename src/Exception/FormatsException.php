<?php

namespace Jenky\LaravelAPI\Exception;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

trait FormatsException
{
    /**
     * Map exception into an JSON response.
     *
     * @param  \Exception $e
     * @param  null|int $statusCode
     * @param  array $headers
     * @return \Illuminate\Http\Response
     */
    public function toJsonResponse(Exception $exception, $statusCode = null, array $headers = [])
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
                'trace' => config('api.trace_as_string') ? explode("\n", $exception->getTraceAsString()) : $e->getTrace(),
            ];
        }

        $exception = $e;

        return array_merge($replacements, $this->getReplacements());
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
        return config('app.debug', false);
    }

    /**
     * Get error format.
     *
     * @return array
     */
    protected function getErrorFormat()
    {
        return config('api.errorFormat', [
            'message' => ':message',
            'type' => ':type',
            'status_code' => ':status_code',
            'errors' => ':errors',
            'code' => ':code',
            'debug' => ':debug',
        ]);
    }

    /**
     * Get user defined replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        return property_exists($this, 'replacements') ? $this->replacements : [];
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
