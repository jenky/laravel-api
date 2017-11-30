<?php

namespace Jenky\LaravelAPI\Http;

use Illuminate\Http\Response as IlluminateResponse;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use Spatie\Fractal\Fractal;

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
     * @param  int $code
     * @param  string|null $message
     * @param  array $headers
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function error($code = 500, $message = null, array $headers = [])
    {
        return abort($code, $message, $headers);
    }

    /**
     * Bind an data to a transformer and start building a response.
     *
     * @param  mixed $data
     * @param  \League\Fractal\TransformerAbstract $transformer
     * @param  \League\Fractal\Serializer\SerializerAbstract|callable|null $serializer
     * @param  callable|null $callback
     * @return $this
     */
    public function fractal($data, TransformerAbstract $transformer, $serializer = null, callable $callback = null)
    {
        $fractal = fractal($data, $transformer);

        if (is_callable($serializer)) {
            return $this->fractalResponse($fractal, $serializer);
        }

        if ($serializer instanceof SerializerAbstract) {
            $fractal->serializeWith($serializer);
        }

        return $this->fractalResponse($fractal, $callback);
    }

    /**
     * Get fractal JSON response.
     *
     * @param  \Spatie\Fractal\Fractal $fractal
     * @param  callable|null $callback
     * @return $this
     */
    protected function fractalResponse(Fractal $fractal, callable $callback = null)
    {
        if ($callback) {
            $fractal = $callback($fractal);
            // return $callback($fractal);
        }

        $this->setContent($fractal->toArray());

        return $this;
    }
}
