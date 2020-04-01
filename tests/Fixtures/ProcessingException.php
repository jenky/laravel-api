<?php

namespace Jenky\LaravelAPI\Test\Fixtures;

use Jenky\LaravelAPI\Contracts\Exception\ErrorException;
use Jenky\LaravelAPI\Exception\HasErrorBag;

class ProcessingException extends \Exception implements ErrorException
{
    use HasErrorBag;
}
