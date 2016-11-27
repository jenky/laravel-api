<?php

namespace Jenky\LaravelAPI\Contracts\Debug;

interface ExceptionHandler
{
    /**
     * Register a new exception handler.
     *
     * @param  callable $callback
     * @return void
     */
    public function register(callable $callback);
}
