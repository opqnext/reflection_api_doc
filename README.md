<p align="center">
    <h1 align="center">reflection_api_doc</h1>
</p>

这是一个基于 thinkphp5 的PHP自动生成api文档的库

[![Latest Stable Version](https://poser.pugx.org/opqnext/reflection-api-doc/v/stable.svg)](https://packagist.org/packages/phpmailer/phpmailer) 
[![Total Downloads](https://img.shields.io/packagist/dt/opqnext/reflection-api-doc.svg)](https://packagist.org/packages/opqnext/reflection-api-doc)
[![Latest Unstable Version](https://poser.pugx.org/opqnext/reflection-api-doc/v/unstable.svg)](https://packagist.org/packages/phpmailer/phpmailer) [![License](https://poser.pugx.org/opqnext/reflection-api-doc/license.svg)](https://packagist.org/packages/opqnext/reflection-api-doc)

两种使用方式，1.composer安装使用。2.独立安装使用。

#####  composer 方式安装

1. 安装：

安装有两种方法:

直接执行:
```
composer require "opqnext/reflection-api-doc:v1.0_beta"
```

或者修改composer.json文件
```
// 在require里加上
"opqnext/reflection-api-doc": "v1.0_beta"

// 可以在文件末加上这个几行 这是国内的镜像下载速度较快。
// 据说每分钟同步，但是我觉得不是
"repositories": {
    "packagist": {
        "type": "composer",
        "url": "https://packagist.phpcomposer.com"
    }
}
```

我的composer.json示例:
```
{
    "name": "topthink/think",
    "description": "the new thinkphp framework",
    "type": "project",
    "keywords": [
        "framework",
        "thinkphp",
        "ORM"
    ],
    "homepage": "http://thinkphp.cn/",
    "license": "Apache-2.0",
    "authors": [
        {
            "name": "liu21st",
            "email": "liu21st@gmail.com"
        }
    ],
    "require": {
        "php": ">=5.4.0",
        "topthink/framework": "^5.0",
        "opqnext/reflection-api-doc": "v1.0_beta"
    },
    "extra": {
        "think-path": "thinkphp"
    },
    "config": {
        "preferred-install": "dist"
    },
    "repositories": {
        "packagist": {
            "type": "composer",
            "url": "https://packagist.phpcomposer.com"
        }
    }
}
```
2. 使用方法

在 application/extra 目录下创建文件名为 documents.php 的配置文件。

配置文件内容如下：

```
<?php
return [
    'title' => "北京想得美科技有限公司",  
    'description' => '"想的美app" | APi接口文档等等。',
    'template' => 'apple', // 苹果绿:apple 葡萄紫:grape
    'class' => [
        'app\index\controller\Article'
        // ...
    ],
];
```
其中 template 为模板类型，暂时提供两种模板风格，分别为苹果绿和葡萄紫，虽然两套模板都是巨丑无比。所以使用的过程中也可以自己开发模板。

**重点:** class 为将要生成文档的类(带命名空间)

2. 示例：

| 注释参数 | 含义 | 说明 |
| - | - | - |
| @title | 标题 | 文档生成的类方法标题 |
| @desc | 描述 | 格式如下，地址、请求方式、备注等 |
| @param | 接收参数 | 格式如下，name:名称、type:类型、required:是否必须、default:默认值、desc:说明 |
| @return | 返回参数 | 格式如下，name:名称、type:类型、required:是否必须、desc:说明、level：层级 |

类的具体实现方法：

```
/**
 * @title 文章接口管理
 */
class Article extends Controller
{
    /**
     * @title 获取文章列表
     * @desc  {"0":"接口地址：http://open.opqnext.com/index.php?c=article&a=index","1":"请求方式：GET","2":"接口备注：必须传入keys值用于通过加密验证"}
     * @param {"name":"page","type":"int","required":true,"default":"1","desc":"页数"}
     * @param {"name":"keys","type":"string","required":true,"default":"xxx","desc":"加密字符串,substr(md5(\"约定秘钥\".$page),8,16)"}
     * @param {"name":"word","type":"string","required":false,"default":"null","desc":"搜索关键字"}
     * @param {"name":"cate","type":"int","required":false,"default":0,"desc":"分类ID,不传表示所有分类"}
     * @param {"name":"size","type":"int","required":false,"default":5,"desc":"每页显示条数，默认为5"}
     * @return {"name":"status","type":"int","required":true,"desc":"返回码：1成功,0失败","level":1}
     * @return {"name":"message","type":"string","required":true,"desc":"返回信息","level":1}
     * @return {"name":"data","type":"array","required":true,"desc":"返回数据","level":1}
     * @return {"name":"id","type":"string","required":true,"desc":"文章ID(22位字符串)","level":2}
     * @return {"name":"title","type":"string","required":true,"desc":"文章标题","level":2}
     * @return {"name":"thumb","type":"string","required":true,"desc":"文章列表图","level":2}
     * @return {"name":"content","type":"text","required":true,"desc":"文章内容","level":2}
     * @return {"name":"cate","type":"int","required":true,"desc":"文章分类","level":2}
     * @return {"name":"tags","type":"array","required":true,"desc":"文章标签","level":2}
     * @return {"name":"id","type":"string","required":true,"desc":"标签ID","level":3}
     * @return {"name":"tag","type":"string","required":true,"desc":"标签名称","level":3}
     * @return {"name":"count","type":"int","required":true,"desc":"标签使用数","level":3}
     * @return {"name":"img","type":"array","required":true,"desc":"文章组图","level":2}
     */
    public function index(){
        //... 具体实现方法
    }
```

编辑好配置文件之后 直接打开浏览器访问 http://localhost/api/documents 即可看到文档页。

demo预览地址:http://beta.tp.opqnext.com:8086/api/documents

注意: 项目中 extend 目录为独立安装使用包。如果你使用composer安装，并且觉得 extend 目录极其碍眼，可以将其删除，并不影响正常使用。\(^o^)/~

#####  独立安装使用

1. 安装
直接下载或者拷贝目录extend下的reflection，放到项目的extend下。

2. 使用方法
同样的，在 application/extra 目录下创建文件名为 documents.php 的配置文件。文件内容如上所示。

在 application 目录下的 common.php 文件中填加如下内容：
```
use think\Route;
Route::get('doc','reflection\Documents@run');
```

编辑好配置文件之后 直接打开浏览器访问 http://localhost/doc 即可看到文档页。

demo预览地址:http://beta.tp.opqnext.com:8086/doc

- 预览

长相一般的苹果绿：

![](https://image.opqnext.com/apple.jpg)

长相也一般的葡萄紫：

![](https://image.opqnext.com/grape.jpg)

![](https://image.opqnext.com/grape_2.png)

4. 支持

如果有使用自动生成文档的需求或者之类的，欢迎加入 QQ群:452209691 共同探讨。



