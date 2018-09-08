<?php

namespace Jenky\LaravelAPI\Exception;

interface TypeException
{
    /**
     * Get exception type.
     *
     * @return string
     */
    public function getType();
}
