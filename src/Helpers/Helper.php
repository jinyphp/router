<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use \Jiny\Core\Registry\Registry;

if (! function_exists('controller')) {
    /**
     * 라우터에서
     * 컨트롤러, 매소드를 실행합니다.
     */
    function controller($controller, $method="index", $param=[])
    {
        $name = "\App\Controllers\\".$controller;
        $app = Registry::get("App");
     
        $controller = new $name ($app);
        Registry::set("controller", $controller);

        return call_user_func([$controller, $method], $param);
    }
}

