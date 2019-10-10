<?php

namespace Jenky\LaravelAPI\Http\Validator;

use Illuminate\Support\Manager;

class ValidatorManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']->get('api.uri_scheme');
    }

    /**
     * Get the domain validator.
     *
     * @return \Jenky\LaravelAPI\Http\Validator\DomainValidator
     */
    protected function createDomainDriver()
    {
        return new DomainValidator(
            $this->app['config']->get('api.domain')
        );
    }

    /**
     * Get the uri prefix validator.
     *
     * @return \Jenky\LaravelAPI\Http\Validator\PrefixValidator
     */
    protected function createPrefixDriver()
    {
        return new PrefixValidator(
            $this->app['config']->get('api.prefix')
        );
    }
}
