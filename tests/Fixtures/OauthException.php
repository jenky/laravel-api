<?php

namespace Jenky\LaravelAPI\Test\Fixtures;

use Jenky\LaravelAPI\Exception\HasExceptionType;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OauthException extends HttpException
{
    use HasExceptionType;
}
