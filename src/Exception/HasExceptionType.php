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
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get exception type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
