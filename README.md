# jiny Router

지니프레임워크의 라우터 입니다. 지니 라우터는 `nikic/fast-route` 페키지를 기반으로 변경하여 작업이 되었습니다.

사용자 커스텀 라우팅은 `app/router/web.php`에서 작성을 할 수 있습니다.


## 뷰 호출
바로 뷰를 호출 할 수 있습니다.

```php
$r->get('/post', function($vars=[]){
    return view("/post", $vars);
});
```

## 컨트롤러 호출
