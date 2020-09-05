<?php

namespace Jiny\Router;
/**
 * json router 설정
 */
class JsonRoute
{
    private $table;
    private $Http;
    private $base = "../route";

    public function __construct($http=null)
    {
        if ($http) {
            $this->Http = $http;
        } else {
            $this->Http = \jiny\http();
        }        
    }
    
    /**
     * 라우터 설정파일 디렉토리 설정
     */
    public function setBase($path)
    {
        $this->base = $path;
        return $this;
    }

    /**
     * 파서를 실행합니다.
     */
    public function main($uri=null)
    {
        if (\is_dir($this->base)) {
            $r = $this->parser($uri);
            return $r;
        } else {
            echo "라우터 디렉토리가 존재하지 않습니다.";
            exit;
        }
    }

    /**
     * uri 에 대한 json 파일을 찾습니다.
     */
    public function parser($uri=null)
    {
        if (!$uri) {
            $uri = $this->Http->endpoint()->uri();
            $uri = \trim($uri,"/");
            $uris =$this->Http->endpoint()->uris();
        } else {
            $uri = \trim($uri,"/");
            $uris = \explode("/", $uri);
        }

        $param = [];
        
        if(empty($uri)) {
            // root index
            if($data = $this->load()) {
                return new \Jiny\Router\Info($data);
            }         
            
        } else {
            //subdir    
            for($i=count($uris); $i>0; $i--) {
                $path = $this->uriPath($uris,$i);
                if($data = $this->isRoute($path)) {
                    $params = \array_reverse($param);
                    // printbr($params);
                    return new \Jiny\Router\Info($data, $params);
                } else {
                    \array_push($param, $uris[$i-1]);
                }
            }
        }

        return null;
        // print("라우터파일이 없습니다.");  
        // exit;   
    }

    /**
     * uri path를 순차적으로 생성합니다.
     * 역순으로 생성
     */
    private function uriPath($uris,$i)
    {
        $str = "";
        for($k=0;$k<$i;$k++) $str .= $uris[$k]."/";
        return $str;
    }

    /**
     * 라우터 파일이 존재하는지 검사합니다.
     */
    private function isRoute($path)
    {
        $path = \trim($path,"/");
        if(file_exists($this->base."/".$path."/index.json")) {
            $data = $this->load($path."/index.json");
            return $data;
        } else if(file_exists($this->base."/".$path.".json")) {
            $data = $this->load($path.".json");
            return $data;
        } 
        return null;
    }

    /**
     * 라우터 json 파일을 읽어 옵니다.
     */
    public function load($filename="index.json")
    {
        if(file_exists($this->base."/".$filename)) {
            $file = \file_get_contents($this->base."/".$filename);
            return \json_decode($file);
        } else {
            // 라우터 파일이 존재하지 않음.
            return null;
        }
    }

    /**
     * 
     */
}