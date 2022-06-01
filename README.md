>Demo样例,请勿用于实际环境,不足之处欢迎指点和纠正,感激不尽

## 流程图

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011714843.png)

## 示例

使用[https://github.com/OWASP/Vulnerable-Web-Application](https://github.com/OWASP/Vulnerable-Web-Application)

文件上传检测

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011714622.png)

XSS检测

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011716647.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011716153.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011717277.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011717048.png)

文件包含/路径穿越/伪协议

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011724322.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011725155.png)

SQL检测

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011734115.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011734859.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011735441.png)

RCE检测

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011736649.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011737019.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011737412.png)

![](https://cdn.jsdelivr.net/gh/AMDyesIntelno/PicGoImg@master/202206011738399.png)

## 检测

### 命令注入&代码注入

检测常见函数

```
file_put_contents
fwrite
exec
passthru
create_function
call_user_func
...
```

检测部分命令

```
whoami
ls
cat
pwd
echo
...
```

检测绕过特征

```
<?php <?=
eval("echo \"hello ".'${${phpinfo()}}'." \";");
echo${IFS}
echo$IFS
\w+\\$\{*\w+
cat</etc/passwd
cat<>/etc/passwd
\w+<>\w+
/b?n/c?t /etc/passwd
\/\w*\?+\w*\/
echo $(whoami)
\\$\(
```

### SQL注入

过滤常用注入手法

```
union select
union[^\xff]+?select[^\xff]+?
updatexml() 或 update操作
update[^\xff]+?\(
extractvalue()
extractvalue[^\xff]+?\(
into dumpfile/outfile
into[^\xff]+?dumpfile
into[^\xff]+?outfile
...
```

过滤常用函数/变量/库

```
load_file[^\xff]+?\(
benchmark[^\xff]+?\(
sleep[^\xff]+?\(
concat[^\xff]+?\(
@@
version[^\xff]+?\(
database[^\xff]+?\(
user[^\xff]+?\(
information_schema\.
mysql\.
sys\.
```

### XSS

```
<img src=/ onerror\n\n=\n\n" alert/**/('xss')">
<[^>]*\s+(?:on|href)\w*\s*=[^`]*(?:top|prompt|alert|confirm)[^`]*\(
<script>/*123*/alert/*123*/('xss')</script>
<[^>]*>[^`]*(?:top|prompt|alert|confirm)[^`]*\(
<svg onload=top["al"+"ert"](1)>
<script src="data:text/html;base64,YWxlcnQoJ3hzcycp"></script>
data伪协议
data:[<MIME-type>][;charset=<encoding>][;base64],<data>
data:
```

### 文件上传监控

后缀白名单&文件内容检测

### 用户操作监控

检测关键词如`admin|login|user`

### 路径穿越,敏感文件读取,PHP伪协议监控

检测关键词如`..|passwd|php://`

## 记录

1. 根据监控模块传递的信息生成日志记录

2. 根据请求方式和Content-type生成原始请求包
