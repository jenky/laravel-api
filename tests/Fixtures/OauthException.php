<?php

namespace Jenky\LaravelAPI\Tests\Fixtures;

use Jenky\LaravelAPI\Exception\HasExceptionType;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OauthException extends HttpException
{
    use HasExceptionType;
}
