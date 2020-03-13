<?php

namespace Jenky\LaravelAPI\Test\Fixtures;

use Jenky\LaravelAPI\Exception\ExceptionWithErrors;
use Jenky\LaravelAPI\Exception\HasErrorBag;

class ProcessingException extends \Exception implements ExceptionWithErrors
{
    use HasErrorBag;
}
