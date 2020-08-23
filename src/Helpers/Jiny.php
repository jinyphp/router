<?php

namespace jiny;

if (! function_exists('route')) {
    function route()
    {
        $obj = \Jiny\Router\JsonRoute::instance();
        return $obj;
    }
}

