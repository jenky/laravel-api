<?php

namespace Jenky\LaravelAPI\Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jenky\LaravelAPI\Contracts\Exception\ErrorException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;

trait FormatsException
{
    /**
     * Map exception into an JSON response.
     *
     * @param  \Throwable $e
     * @param  int|null $statusCode
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJsonResponse(Throwable $exception, ?int $statusCode = null, array $headers = [])
    {
        $replacements = $this->prepareReplacements(
            $exception, $statusCode, $headers
        );

        $response = $this->getErrorFormat();

        array_walk_recursive($response, function (&$value) use ($replacements) {
            if (Str::startsWith($value, ':') && isset($replacements[$value])) {
                $value = $replacements[$value];
            }
        });

        /** @var \Symfony\Component\ErrorHandler\Exception\FlattenException $exception */
        return new JsonResponse(
            $this->removeEmptyReplacements($response),
            $exception->getStatusCode(),
            $exception->getHeaders(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Prepare the replacements array by gathering the keys and values.
     *
     * @param  \Throwable $exception
     * @param  int|null $statusCode
     * @param  array $headers
     * @return array
     */
    protected function prepareReplacements(Throwable &$exception, ?int $statusCode = null, array $headers = []): array
    {
        $e = FlattenException::createFromThrowable($exception, $statusCode, $headers);

        $replacements = [
            ':message' => $e->getMessage() ?: $e->getStatusText(),
            ':status_code' => $e->getStatusCode(),
            ':type' => method_exists($exception, 'getType') ? $exception->getType() : class_basename($e->getClass()),
            ':code' => $e->getCode(),
        ];

        if ($exception instanceof ValidationException) {
            $validator = $exception->validator;

            if ($validator->errors()->isNotEmpty()) {
                $replacements[':errors'] = $validator->errors();
            }
        }

        if ($exception instanceof ErrorException) {
            if (! empty($exception->getErrors())) {
                $replacements[':errors'] = $exception->getErrors();
            }
        }

        if ($this->runningInDebugMode()) {
            $replacements[':debug'] = $this->appendDebugInformation($e);
        }

        $exception = $e;

        return array_merge($replacements, $this->getReplacements());
    }

    /**
     * Appends debug information.
     *
     * @param  \Symfony\Component\ErrorHandler\Exception\FlattenException $e
     * @return array
     */
    protected function appendDebugInformation(FlattenException $e): array
    {
        $trace = $this->config('api.trace.as_string', false)
            ? explode("\n", $e->getTraceAsString())
            : ($this->config('api.trace.include_args', false)
                ? $e->getTrace()
                : array_map(function ($item) {
                    return Arr::except($item, ['args']);
                }, $e->getTrace())
            );

        if ($size = $this->config('api.trace.size_limit', 0)) {
            $trace = array_splice($trace, 0, $size);
        }

        return [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'class' => $e->getClass(),
            'trace' => $trace,
        ];
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
     * Determines if the application are running in debug mode.
     *
     * @return bool
     */
    protected function runningInDebugMode(): bool
    {
        return (bool) $this->config('app.debug', false);
    }

    /**
     * Get error format.
     *
     * @return array
     */
    protected function getErrorFormat(): array
    {
        return $this->config('api.error_format', [
            'message' => ':message',
            'type' => ':type',
            'status_code' => ':status_code',
            'errors' => ':errors',
            'code' => ':code',
            'debug' => ':debug',
        ]);
    }

    /**
     * Get the config instance or value.
     *
     * @param  string|null  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    protected function config(?string $key = null, $default = null)
    {
        $config = $this->container->make('config');

        return $key ? $config->get($key, $default) : $config;
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
