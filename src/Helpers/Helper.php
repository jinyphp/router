<?php
use \Jiny\Core\Registry\Registry;

if (! function_exists('controller')) {
    /**
     * 라우터에서
     * 컨트롤러, 매소드를 실행합니다.
     */
    function controller($controller, $method="index", $param=[])
    {
        //echo "<br>컨트롤러를 호출합니다. $controller $method <br>";
        //print_r($param);
        //echo "<br>";

        $name = "\App\Controllers\\".$controller;
        $app = Registry::get("App");
     
        $controller = new $name ($app);
        Registry::set("controller", $controller);

        return call_user_func([$controller, $method], $param);

    }
}