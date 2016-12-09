<?php

namespace Jenky\LaravelAPI\Http;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response as IlluminateResponse;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;
use Spatie\Fractal\Fractal;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * Return an error response.
     *
     * @param  string $message
     * @param  int $statusCode
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function error($message, $statusCode)
    {
        throw new HttpException($statusCode, $message);
    }

    /**
     * Return a 400 bad request error.
     *
     * @param  string $message
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function badRequest($message = 'Bad Request')
    {
        return $this->error($message, 400);
    }

    /**
     * Return a 401 unauthorized error.
     *
     * @param  string $message
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function unauthorized($message = 'Unauthorized')
    {
        return $this->error($message, 401);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param  string $message
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function forbidden($message = 'Forbidden')
    {
        return $this->error($message, 403);
    }

    /**
     * Return a 404 not found error.
     *
     * @param  string $message
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function notFound($message = 'Not Found')
    {
        return $this->error($message, 404);
    }

    /**
     * Return a 422 unprocessable entity error.
     *
     * @param  string $message
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function unprocessable($message = 'Unprocessable Entity')
    {
        return $this->error($message, 422);
    }

    /**
     * Return a 500 internal server error.
     *
     * @param  string $message
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return void
     */
    public function internalError($message = 'Internal Error')
    {
        return $this->error($message, 500);
    }

    /**
     * Bind an item to a transformer and start building a response.
     *
     * @param  object|array $data
     * @param  \League\Fractal\TransformerAbstract $transformer
     * @param  \Closure $callback
     * @return $this
     */
    public function item($data, TransformerAbstract $transformer, callable $callback = null)
    {
        return $this->fractalResponse(fractal()->item($data, $transformer), $callback);
    }

    /**
     * Bind an collection/multidimensional array to a transformer and start building a response.
     *
     * @param  \Illuminate\Support\Collection|array $data
     * @param  \League\Fractal\TransformerAbstract $transformer
     * @param  \Closure $callback
     * @return $this
     */
    public function collection($data, TransformerAbstract $transformer, callable $callback = null)
    {
        return $this->fractalResponse(fractal()->collection($data, $transformer), $callback);
    }

    /**
     * Bind an paginator to a transformer and start building a response.
     *
     * @param  \Illuminate\Support\Collection|array $data
     * @param  \League\Fractal\TransformerAbstract $transformer
     * @param  \Closure $callback
     * @return $this
     */
    public function paginator(LengthAwarePaginator $data, TransformerAbstract $transformer, callable $callback = null)
    {
        $fractal = fractal()->collection($data->getCollection(), $transformer)
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $this->fractalResponse($fractal, $callback);
    }

    /**
     * Bind an data to a transformer and start building a response.
     *
     * @param  mixed $data
     * @param  \League\Fractal\TransformerAbstract $transformer
     * @param  \Closure $callback
     * @return $this
     */
    public function transform($data, TransformerAbstract $transformer, callable $callback = null)
    {
        if ($data instanceof LengthAwarePaginator) {
            return $this->paginator($data, $transformer, $callback);
        }

        return $this->fractalResponse(fractal($data, $transformer), $callback);
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
            // $fractal = $callback($fractal);
            return $callback($fractal);
        }

        $this->setContent($fractal->toArray());

        return $this;
    }

    /**
     * Set response status code.
     *
     * @param  int $statusCode
     * @return $this
     */
    public function statusCode($statusCode)
    {
        $this->setStatusCode($statusCode);

        return $this;
    }
}
