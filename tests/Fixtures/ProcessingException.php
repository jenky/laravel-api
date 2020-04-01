<?php

namespace Jenky\LaravelAPI\Tests\Fixtures;

use Jenky\LaravelAPI\Contracts\Exception\ErrorException;
use Jenky\LaravelAPI\Exception\HasErrorBag;

class ProcessingException extends \Exception implements ErrorException
{
    use HasErrorBag;
}
