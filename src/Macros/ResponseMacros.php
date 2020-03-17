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
     * Make a response and associate a location and/or content if provided.
     *
     * @param  int $status
     * @param  mixed $content
     * @param  null|string $location
     * @return $this
     */
    public function responseWithContentAndLocation()
    {
        return function ($status, $content = null, $location = null) {
            $response = response($content, $status);

            if ($location) {
                $response->header('Location', $location);
            }

            return $response;
        };
    }
}
