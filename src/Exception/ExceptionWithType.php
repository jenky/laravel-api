<?php

namespace Jenky\LaravelAPI\Exception;

interface ExceptionWithType
{
    /**
     * Get exception type.
     *
     * @return string
     */
    public function getType(): string;
}
