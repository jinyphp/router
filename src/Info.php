<?php

namespace Jiny\Router;
/**
 * DTO
 */
class Info
{
    public $route;
    public $params=[];
    public function __construct($r, $p=[])
    {
        $this->route = $r;
        $this->params = $p;
    }
}