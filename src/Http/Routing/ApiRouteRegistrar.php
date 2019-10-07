<?php

namespace Jenky\LaravelAPI\Http\Routing;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

class ApiRouteRegistrar extends RouteRegistrar
{
    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct($router);

        $this->allowedAttributes[] = 'version';
    }
}
