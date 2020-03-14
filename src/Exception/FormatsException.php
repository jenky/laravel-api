<?php

namespace Jenky\LaravelAPI\Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait FormatsException
{
    /**
     * Map exception into an JSON response.
     *
     * @param  \Throwable $e
     * @param  null|int $statusCode
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJsonResponse(Throwable $exception, ?int $statusCode = null, array $headers = [])
    {
        $replacements = $this->prepareReplacements($exception, $statusCode, $headers);
        $response = $this->getErrorFormat();

        array_walk_recursive($response, function (&$value) use ($replacements) {
            if (Str::startsWith($value, ':') && isset($replacements[$value])) {
                $value = $replacements[$value];
            }
        });

        $response = $this->removeEmptyReplacements($response);

        /** @var \Symfony\Component\ErrorHandler\Exception\FlattenException $exception */
        return new JsonResponse(
            $response,
            $exception->getStatusCode(),
            $exception->getHeaders(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Prepare the replacements array by gathering the keys and values.
     *
     * @param  \Throwable $exception
     * @param  null|int $statusCode
     * @param  array $headers
     * @return array
     */
    protected function prepareReplacements(Throwable &$exception, ?int $statusCode = null, array $headers = []): array
    {
        $e = FlattenException::createFromThrowable($exception, $statusCode, $headers);
        $statusCode = $e->getStatusCode();

        $replacements = [
            ':message' => $e->getMessage() ?: Arr::get(Response::$statusTexts, $statusCode),
            ':status_code' => $statusCode,
            ':type' => class_basename($e->getClass()),
            ':code' => $e->getCode(),
        ];

        if ($exception instanceof ValidationException) {
            $validator = $exception->validator;

            if (! $validator->errors()->isEmpty()) {
                $replacements[':errors'] = $validator->errors();
            }
        }

        if ($exception instanceof ExceptionWithErrors) {
            if (! $exception->getErrors()->isEmpty()) {
                $replacements[':errors'] = $exception->getErrors();
            }
        }

        if ($exception instanceof ExceptionWithType) {
            if ($type = $exception->getType()) {
                $replacements[':type'] = $type;
            }
        }

        if ($this->runningInDebugMode()) {
            $trace = config('api.trace.as_string', false)
                ? explode("\n", $exception->getTraceAsString())
                : (config('api.trace.include_args', false)
                    ? $e->getTrace()
                    : collect($e->getTrace())->map(function ($item) {
                        return Arr::except($item, ['args']);
                    })->all()
                );

            if ($size = config('api.trace.size_limit', 0)) {
                $trace = array_splice($trace, 0, $size);
            }

            $replacements[':debug'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'class' => $e->getClass(),
                'trace' => $trace,
            ];
        }

        $exception = $e;

        return array_merge($replacements, $this->getReplacements());
    }

    /**
     * Recursively remove any empty replacement values in the response array.
     *
     * @param  array $input
     * @return array
     */
    protected function removeEmptyReplacements(array $input): array
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->removeEmptyReplacements($value);
            }
        }

        return array_filter($input, function ($value) {
            if (is_string($value)) {
                return ! Str::startsWith($value, ':');
            }

            return true;
        });
    }

    /**
     * Determines if we are running in debug mode.
     *
     * @return bool
     */
    protected function runningInDebugMode(): bool
    {
        return (bool) config('app.debug', false);
    }

    /**
     * Get error format.
     *
     * @return array
     */
    protected function getErrorFormat(): array
    {
        return config('api.error_format', [
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
    public function getReplacements(): array
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
