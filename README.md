# reflection_api_doc

将会是一个基于 thinkphp5 的PHP自动生成api文档的库

1. 使用方法：

在 extra 目录下创建文件名为 documents.php 的配置文件。

配置文件内容如下：

```
<?php
return [
    'title' => "北京想得美科技有限公司",  
    'description' => '"想的美app" | APi接口文档等等。',
    'template' => 'apple', // 苹果绿:apple 葡萄紫:grape
    'class' => [
        'app\index\controller\Demo',
        'app\index\controller\Product',
        'app\index\controller\Store',
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
| @param | 接收参数 | 格式如下，名称、类型、是否必须、默认值、说明等 |
| @return | 返回参数 | 格式如下，名称、类型、是否必须、说明等 |

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
3. 预览

长相一般的苹果绿：

![](https://image.opqnext.com/apple.jpg)

长相也一般的葡萄紫：

![](https://image.opqnext.com/grape.jpg)

![](https://image.opqnext.com/grape_2.png)

4. 支持
如果有使用自动生成文档的需求或者之类的，欢迎加入 QQ群:452209691 共同探讨。



