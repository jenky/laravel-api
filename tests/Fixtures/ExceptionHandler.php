<?php

namespace Jenky\LaravelAPI\Tests\Fixtures;

use Jenky\LaravelAPI\Exception\ExceptionResponse;
use Orchestra\Testbench\Exceptions\Handler;

class ExceptionHandler extends Handler
{
    use ExceptionResponse;
}
