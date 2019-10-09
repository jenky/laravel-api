<?php

namespace Jenky\LaravelAPI\Test\Fixtures;

use Jenky\LaravelAPI\Exception\ExceptionResponse;
use Orchestra\Testbench\Exceptions\Handler;

class ExceptionHandler extends Handler
{
    use ExceptionResponse;
}
