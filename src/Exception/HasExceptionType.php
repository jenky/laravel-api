<?php

namespace Jenky\LaravelAPI\Exception;

trait HasExceptionType
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Get exception type.
     *
     * @param  string
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get exception type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
