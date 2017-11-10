<?php

namespace Jenky\LaravelAPI\Http;

use Illuminate\Http\Response as IlluminateResponse;

class Response extends IlluminateResponse
{
    /**
     * Respond with a created response and associate a location and/or content if provided.
     *
     * @param  mixed $content
     * @param  null|string $location
     * @return $this
     */
    public function created($content = null, $location = null)
    {
        return $this->responseWithContentAndLocation(201, $content, $location);
    }

    /**
     * Respond with an accepted response and associate a location and/or content if provided.
     *
     * @param  mixed $content
     * @param  null|string $location
     * @return $this
     */
    public function accepted($content = null, $location = null)
    {
        return $this->responseWithContentAndLocation(202, $content, $location);
    }

    /**
     * Make a response and associate a location and/or content if provided.
     *
     * @param  int $status
     * @param  mixed $content
     * @param  null|string $location
     * @return $this
     */
    protected function responseWithContentAndLocation($status, $content = null, $location = null)
    {
        if ($content) {
            $this->setContent($content);
        }

        if ($location) {
            $this->header('Location', $location);
        }

        $this->setStatusCode($status);

        return $this;
    }

    /**
     * Respond with a no content response.
     *
     * @return $this
     */
    public function noContent()
    {
        $this->setStatusCode(204);

        return $this;
    }

    /**
     * Return an error.
     *
     * @param  string $message
     * @param  array $headers
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function error($code = 500, $message = 'Server error', array $headers = [])
    {
        return abort($code, $message, $headers);
    }
}
