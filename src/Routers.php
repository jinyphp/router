<?php

namespace Jiny\Router;

use \Jiny\Core\Registry\Registry;
use \Jiny\Core\Base\File;

//FastRoute Copy
use \Jiny\Router\Dispatcher;
use \Jiny\Router\Dispatcher\GroupCountBased;

/**
 * 커스텀 라우트 파일을 찾아 읽어옵니다.
 */
class Routers
{
    public $App;
    
    private $_urlArr = [];
    private $_routeFile;

    
    public $_viewFile;
    public $_param;

    public function __construct($app)
    {
        \TimeLog::set(__CLASS__);
        $this->App = $app;
        // echo "라우터 초기화<br>";
    }

    /**
     * 라우트 설정을 검사합니다.
     */

    public function routing()
    {   
        // 콜백함수 선언
        $func = function($r) {

            $filename = ROOT.conf("ENV.path.route").DS."web.php";     
            $filename = File::path($filename);
            if (file_exists($filename)) {
                include $filename;
            }            
        };
       
        // 라우터를 분석합니다.
        $dispatcher = $this->Dispatcher($func);

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

     
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                //echo "route ... 404 Not Found<br><br>";
                return $this->App->run();
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                //echo "route ... 405 Method Not Allowed<br><br>";
                return $this->App->run();
                break;

            case Dispatcher::FOUND:
                // 라우터 처리동작

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                // 익명함수 호출
                if(is_callable($handler)){
                    return $handler($vars);
                } 
                // 문자열
                else if(is_string($vars)){
                    return $handler;
                }

                break;
        }

    }

    


    /**
     * 
     */
    function Dispatcher(callable $Callback, array $options = [])
    {
        $routeCollector = new \Jiny\Router\RouteCollector();

        // 라우트를 정의작업
        // 콜백으로 처리합니다.
        $Callback($routeCollector);

        return new GroupCountBased($routeCollector->getData());
       
    }



    public function root()
    {
        //echo "urls 가 비어 있습니다.";
    }

    public function parser($arr)
    {
        //echo "routing parser";
        $path=$this->isRoute($arr);

        if (isset($this->_routeFile)) {      
            $this->loadRoute($this->_routeFile);
            $this->param($path); 
        } 
    }

    public function isRoute($arr)
    {
        // $base = "../app/route/";  
        $base = ROOT.Registry::get("CONFIG")->data("ENV.path.route").DS;
        $base = str_replace("/",DS,$base);
     
        $path = $base;
        foreach ($arr as $name) {
            $path .= $name;
            if (\file_exists($path.".php")) {
                $this->_routeFile = $path;                  
            }
            $path .= "_";
        }

        return $path;
    }



    /**
     * 라우트 파일이 시스템에 영향이 없도록
     * 설정값 반환형태로 처리합니다.
     */
    public function loadRoute($filename)
    {
        // 커스텀 파일을 읽어 옵니다.
        echo "커스텀 파일 읽어 옵니다.<br>";
        //include $filename.".php";
        echo $filename.".php";

    }

    /**
     * url에서 파라미터 값을 추출합니다.
     */
    public function param($path)
    {
        $path = str_replace($this->_routeFile,"",$path);         
        $this->_param = explode("_",$path);
    }


    /**
     * 
     */
}