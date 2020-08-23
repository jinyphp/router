<?php

namespace Jiny\Router;
/**
 * DTO
 */
class Info
{
    public $route;
    public $params = [];
    public function __construct($r, $params=[])
    {
        $this->route = $r;
        $this->params = $params;
    }
}