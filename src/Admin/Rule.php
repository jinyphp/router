<?php

namespace Jiny\Router\Admin;
/**
 * 라우트관리
 */
class Rule
{
    public function __construct()
    {
        //echo __CLASS__;
    }

    private $base = "../route";
    public function main($params=[])
    {
        $method = \jiny\http\request()->method();
        if ($method == "POST") {
            //printbr($_POST);
            $filename = $this->base."/".$_POST['filename'];
            
            // JSON_UNESCAPED_UNICODE : 한글출력
            // JSON_UNESCAPED_SLASHES : 역슬래쉬
            // JSON_PRETTY_PRINT : 출력포맷
            $json = \json_encode($_POST['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            \file_put_contents($filename, $json);
            //var_dump($json);

            $url = "/admin/route";
            // post redirect get pattern
            header("HTTP/1.1 301 Moved Permanently");
            header("location:".$url);
            
        } else {
            // print_r($params);
            if (isset($params[0]) == "new") {
                // 신규추가
                
                echo $this->newRoute();

            } else {
                if(isset($_GET['path'])) {
                    $body = $this->edit();
                    $resource = \file_get_contents("../resource/admin/route_edit.html");
                    $resource = str_replace("{{form_items}}",$body,$resource);
                    echo $resource;
        
                } else {
                    $body = $this->list();
                    $resource = \file_get_contents("../resource/admin/route.html");
                    $resource = str_replace("{{routelist}}",$body,$resource);
                    echo $resource;
                }
            }            
        }
        
        
    }

    public function newRoute()
    {
        // echo "신규추가";
        $resource = \file_get_contents("../resource/admin/route_new.html");
        $resource = str_replace("{{routelist}}",$body,$resource);
        return $resource;
    }

    public function edit()
    {
        //echo "라우트 목록<br>";
        $path = $_GET['path'].".json";
        $filename = str_replace("-","/",$path);
        $body = file_get_contents($this->base."/".$filename);
        $obj = json_decode($body);
        // print_r($json);
        
        // 리스트 출력
        $form = "<form action='/admin/route' method='POST'>";
        $form .= "<input type='hidden' name='filename' value='".$filename."'>";
        $form .= $this->objInput($obj);
        $form .= "<button type='submit'>수정</button>";
        $form .= "</form>";

        return $form;
    }

    public function objInput($obj, $prob=null)
    {
        $str = "<ul>";
        foreach($obj as $key => $value) {
            $str .= "<li>
                    <div class='form-group'>";
            if(\is_object($value)) {
                $str .= $key;
                $str .= $this->objInput($value, $prob."".$key."");
            } else {
                $str .= $this->label($key);
                if($prob) {
                    $str .= "<input type=text class='form-control' name='data[".$prob."][".$key."]' value='$value'>";
                } else {
                    $str .= "<input type=text class='form-control' name='data[$key]' value='$value'>";
                }
            }
            $str .= "</div>
            </li>";
        }
        $str .= "</ul>";
        return $str;
    }



    public function label($str)
    {
        return "<label class='col-sm-2 col-form-label'>".$str."</label>";
    }



    public function list($path="", $level=1)
    {
        $str = "<ul class='list-group'>";
        $r = scandir($this->base."/".$path);
        $r = $this->sort($r);
        foreach ($r as $value) {
            if ($value == "." || $value == "..") continue;
            $str .= $this->list_li($path, $value, $level);
        }
        $str .= "</ul>";
        return $str;
    }

    private function list_li($path, $value, $level)
    {
        $str = "<li class='list-group-item'>"; 
        if (\is_dir($this->base."/".$path."/".$value)) {
            $str .= $value;
            $str .= $this->list($path."/".$value, $level++);
        } else {
            $str .= $this->alink($path, $value);
        }
        $str .= "</li>";
        return $str;
    }

    private function alink($path, $value)
    {
        $link = $this->link($path, $value);
        $href = "?path=".$link;

        $alink = "<a href='".$href."'>".$value."</a>";
        return $alink;
    }

    /**
     * 링크주소 변경
     * '/' => '-'
     */
    private function link($path, $value)
    {
        $link = str_replace(["/",".json"],"-",$path."/".$value);
        return trim($link,"-");
    }

    private function sort($dir)
    {
        $arr = [];
        foreach ($dir as $value) {
            if ($value == "." || $value == "..") continue;
            if ($value == "index.json") {
                \array_unshift($arr, $value);
            } else {
                \array_push($arr,$value);
            }           
        }
        return $arr;
    }

    /**
     * 
     */
}