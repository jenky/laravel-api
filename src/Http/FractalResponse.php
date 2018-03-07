<?php

namespace Jenky\LaravelAPI\Http;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;

class FractalResponse implements Responsable
{
    /**
     * The underlying resource.
     *
     * @var mixed
     */
    public $resource;

    /**
     * @var \League\Fractal\TransformerAbstract
     */
    protected $transformer;

    /**
     * @var \League\Fractal\Serializer\SerializerAbstract
     */
    protected $serializer;

    /**
     * @var \Spatie\Fractalistic\Fractal
     */
    protected $fractal;

    /**
     * @var array
     */
    protected $includes;

    /**
     * Create a new fractal response.
     *
     * @param  mixed $resource
     * @param  \League\Fractal\TransformerAbstract $transformer
     * @param  \League\Fractal\Serializer\SerializerAbstract|null $serializer
     * @return void
     */
    public function __construct($resource, TransformerAbstract $transformer, SerializerAbstract $serializer = null)
    {
        $this->transformer = $transformer;
        $this->serializer = $serializer;
        $this->resource = tap($resource, function ($data) {
            return $this->withResource($data);
        });

        $this->fractal = fractal($this->resource, $this->transformer, $this->serializer);
    }

    /**
     * Get the status code for the response.
     *
     * @return int
     */
    public function status()
    {
        return $this->resource instanceof Model &&
               $this->resource->wasRecentlyCreated ? 201 : 200;
    }

    /**
     * Get the headers for the response.
     *
     * @return array
     */
    public function headers()
    {
        return [];
    }

    /**
     * Customize the resource for response.
     *
     * @param  mixed $resource
     * @return void
     */
    public function withResource($resource)
    {
        //
    }

    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Http\JsonResponse $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        //
    }

    /**
     * Get the response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function response()
    {
        return new JsonResponse($this->fractal->toArray(), $this->status(), $this->headers());
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return tap($this->response(), function ($response) use ($request) {
            $this->withResponse($request, $response);
        });
    }

    /**
     * Get transformer includes.
     *
     * @return array
     */
    protected function getTransformerIncludes()
    {
        return array_merge($this->transformer->getAvailableIncludes(), $this->transformer->getDefaultIncludes());
    }

    /**
     * Get requested includes.
     *
     * @return array
     */
    protected function getRequestedIncludes()
    {
        return explode(',', request(config('fractal.auto_includes.request_key')));
    }

    /**
     * Get "valid" includes.
     *
     * @return array
     */
    protected function getIncludes()
    {
        if (is_null($this->includes)) {
            $includes = $this->transformer->getDefaultIncludes();

            foreach ($this->getRequestedIncludes() as $include) {
                if (in_array($include, $this->transformer->getAvailableIncludes())) {
                    $includes[] = $include;
                }
            }

            $this->includes = array_unique($includes);
        }

        return $this->includes;
    }

    /**
     * Include resources with callback functions.
     *
     * @param  mixed $resource
     * @return void
     */
    protected function includeResources($resource)
    {
        foreach ($this->getIncludes() as $include) {
            $method = 'include'.Str::studly($include);
            if (method_exists($this, $method)) {
                $this->{$method}($resource);
            }
        }
    }

    /**
     * Dynamically pass method calls to the underlying fractal.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->fractal->{$method}(...$parameters);
    }
}
