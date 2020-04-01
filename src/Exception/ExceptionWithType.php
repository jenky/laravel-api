<?php

namespace Jenky\LaravelAPI\Exception;

interface ExceptionWithType extends \Throwable
{
    /**
     * Get exception type.
     *
     * @return string
     */
    public function getType(): string;
}
