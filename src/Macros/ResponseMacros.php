<?php

namespace Jenky\LaravelAPI\Macros;

class ResponseMacros
{
    /**
     * Respond with a created response and associate a location and/or content if provided.
     *
     * @param  mixed $content
     * @param  null|string $location
     * @return $this
     */
    public function created()
    {
        return function ($content = null, $location = null) {
            return $this->responseWithContentAndLocation(201, $content, $location);
        };
    }

    /**
     * Respond with an accepted response and associate a location and/or content if provided.
     *
     * @param  mixed $content
     * @param  null|string $location
     * @return $this
     */
    public function accepted()
    {
        return function ($content = null, $location = null) {
            return $this->responseWithContentAndLocation(202, $content, $location);
        };
    }

    /**
     * Respond with a no content response.
     *
     * @return $this
     */
    public function noContent()
    {
        return function () {
            $this->setStatusCode(204);

            return $this;
        };
    }

    /**
     * Make a response and associate a location and/or content if provided.
     *
     * @param  int $status
     * @param  mixed $content
     * @param  null|string $location
     * @return $this
     */
    protected function responseWithContentAndLocation()
    {
        return function ($status, $content = null, $location = null) {
            $response = response($content, $status);

            if ($location) {
                $response->header('Location', $location);
            }

            return $response;
        };
    }

    /**
     * Return an error.
     *
     * @param  int $code
     * @param  string|null $message
     * @param  array $headers
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function error()
    {
        return function ($code = 500, $message = null, array $headers = []) {
            return abort($code, $message, $headers);
        };
    }
}
