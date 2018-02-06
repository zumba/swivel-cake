<?php

abstract class SwivelLoaderProxy {

    /**
     * A swivel object to be proxied.
     *
     * @var mixed
     */
    protected $proxy;

    /**
     * Proxy calls to the swivel object that don't need to be tracked.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        call_user_func_array([ $this->proxy, $name ], $arguments);
        return $this;
    }
}
