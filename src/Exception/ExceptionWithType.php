<?php

namespace Jenky\LaravelAPI\Exception;

interface ExceptionWithType
{
    /**
     * Get exception type.
     *
     * @param  string
     * @return mixed
     */
    public function setType($type);

    /**
     * Get exception type.
     *
     * @return string
     */
    public function getType();
}
