<?php

namespace Jiny\Router;
/**
 * json router 설정
 */
class Json
{
    private $table;
    public function __construct()
    {
        $this->load();
    }

    /**
     * 라우터 json 파일을 읽어 옵니다.
     */
    public function load($filename="../Route/root.json")
    {
        //라우트 테이블 읽기
        $jsonfile = \file_get_contents($filename);
        $this->table = \json_decode($jsonfile);
        //print_r($this->table);
        return $this;
    }



    private $default;
    public function parser($uri)
    {
        $obj = $this->table[1];
        
        // 접속한 url경로가 root인경우,
        if(count($uri)==0) {
            $this->default = $this->table[0]; // root
        }        

        //echo "<br>";
        // print_r($uri);
        foreach($uri as $key) {
            $key = \strtolower($key);
            // echo "키=".$key;
            
            if(isset($obj->$key) && \is_array($obj->$key)) {
                $this->default = $obj->$key[0];
                if (is_object($obj->$key[1])) {
                    //echo ">> 객체분석";
                    $obj = $obj->$key[1];
                } else {
                    // 배열의 첫번째값 기본값
                    return $this->default;
                }

            } else if (isset($obj->$key) && is_object($obj->$key)) {
                //echo "객체파싱";
                $obj = $obj->$key;

            } else {
                return $this->isExist($obj, $key);
            }
            //echo "<br>";
        }
        //echo "파싱 종료";
        return $this->default;
    }

    private function isArray($obj)
    {

    }

    private function isExist($obj, $key)
    {
        if (\property_exists($obj, $key)) {
            return $obj->$key;
        } else {
            if(isset($this->default)) {
                //echo "기본값으로 대체";
                return $this->default;
            }
            echo "route 분석을 할 수 없습니다. json 설정파일을 확인해 주세요.";
            exit;
        }
    }

}