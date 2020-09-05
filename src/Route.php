<?php

namespace Jiny\Router;
/**
 * 
 */
class Route
{
    use \Jiny\Petterns\Singleton; // 싱글턴 패턴 적용

    public function init()
    {
        $Http = \jiny\http();
        $this->Router = new \Jiny\Router\JsonRoute($Http);  
    }

    private $Router;
    private $r;
    public function main($uri=null)
    {
        $this->r = $this->Router->main($uri);
        return $this;
    }
    
    public function get()
    {
        return $this->r;
    }

    /**
     * 인증접속 허용여부 설정값 반환
     */
    public function auth()
    {
        return $this->r->route->auth;
    }

    /**
     * 라우트 활성화
     */
    public function enable()
    {
        if(is_object($this->r)) return $this->r->route->enable;
    }

    public function actionType()
    {
        if(isset($this->r->route->action->type)) return $this->r->route->action->type;
    }

    public function actionConf()
    {   
        if(isset($this->r->route->action->conf)) return $this->r->route->action->conf;
    }

    public function theme()
    {
        if(isset($this->r->route->theme->name)) return $this->r->route->theme->name;
    }



    /*
    public function controller()
    {
        $name = $this->controllerName();
        return new $name;
    }
    */

    public function controllerName()
    {
        if (isset($this->r->route->controller->name)) {
            return $this->r->route->controller->name;
        } else {
            // 컨틀롤러 이름이 없음
            return null;
        }
    }

    public function apiName()
    {
        if (isset($this->r->route->api->name)) {
            return $this->r->route->api->name;
        } else {
            // 컨틀롤러 이름이 없음
            return null;
        }
    }

    public function method()
    {
        if(isset($this->r->route->controller->method)) {
            return $this->r->route->controller->method;
        }
        return "main";
    }

    /**
     * uri 파라미터
     */
    public function params()
    {
        return $this->r->params;
    }
    
}