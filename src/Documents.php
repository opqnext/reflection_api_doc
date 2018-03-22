<?php
/**
 * 自动生成文档类
 * @author opq.next
 * @date 2017-08-28
 */

namespace Reflection\Api\Doc;

use think\Config;
use think\Request;
use think\View;

class Documents
{
    private $view;
    private $config;
    private $request;

    private $template = [
        'type'         => 'Think',
        'view_path'    => '',
        'view_suffix'  => 'html',
        'view_depr'    => DS,
        'tpl_begin'    => '{',
        'tpl_end'      => '}',
        'taglib_begin' => '{',
        'taglib_end'   => '}',
    ];

    public function __construct()
    {
        $this->request = Request::instance();
        $this->template['view_path'] = __DIR__.DS.'view'.DS;
        $this->view = View::instance($this->template,[]);
        $this->config = Config::get('documents');
    }

    /**
     * 接口的列表页
     * @author opqnext
     * @date 2017-8-15
     */
    public function run(){
        $this->is_config();
        $class = $this->config['class'];
        $result = $data = array();
        foreach ($class as $val){
            $methods = $this->getMethods($val,'public');
            foreach($methods as $k=>$v){
                $meth_v = $this->Item($val,$v);
                $meth_v['name'] = $v;

                $methods[$k] = $meth_v;
            }
            $data['title'] = $this->Ctitle($val);
            $data['class'] = $val;
            $data['param'] = str_replace('\\','-',$val);
            $data['method'] = $methods;
            $result[] = $data;

        }
        $this->view->assign('list', $result);
        $this->view->assign('title', $this->config['title']);
        $this->view->assign('description', $this->config['description']);
        if(is_file($this->template['view_path'].$this->config['template'].'.html')){
            return $this->view->fetch($this->config['template']);
        } else {
            return $this->view->fetch('error');
        }
    }

    private function is_config()
    {
        if(!$this->config) {
            echo "<h1>没有找到配置文件 documents.php</h1>";
            return;
        }
    }

    private function Item($class,$method)
    {
        $re = new Reflection($class);
        $res = $re->getMethod($method);
        $item = $this->getData($res);
        return [
            'title'=>isset($item['title'])&&!empty($item['title'])?$item['title']:'未配置标题',
            'desc'=>isset($item['desc'])&&!empty($item['desc'])?$item['desc']:'未配置描述信息',
            'params'=>isset($item['params'])&&!empty($item['params'])?$item['params']:[],
            'returns'=>isset($item['returns'])&&!empty($item['returns'])?$item['returns']:[],
        ];
    }

    /**
     * 获取类名称
     * @param $class
     * @return mixed
     */
    public function Ctitle($class){
        $re = new Reflection($class);
        $res = $re->getClass();
        $item = $this->getData($res);
        return $item['title'];
    }

    /**
     * 获取类中非继承方法和重写方法
     * 只获取在本类中声明的方法，包含重写的父类方法，其他继承自父类但未重写的，不获取
     * 例
     * class A{
     *      public function a1(){}
     *      public function a2(){}
     * }
     * class B extends A{
     *      public function b1(){}
     *      public function a1(){}
     * }
     * getMethods('B')返回方法名b1和a1，a2虽然被B继承了，但未重写，故不返回
     * @param string $classname 类名
     * @param string $access public or protected  or private or final 方法的访问权限
     * @return array(methodname=>access)  or array(methodname) 返回数组，如果第二个参数有效，
     * 则返回以方法名为key，访问权限为value的数组
     * @see  使用了命名空间，故在new 时类前加反斜线；如果此此函数不是作为类中方法使用，可能由于权限问题，
     *   只能获得public方法
     */
    public function getMethods($classname,$access=null){
        $class = new \ReflectionClass($classname);
        $methods = $class->getMethods();
        $returnArr = array();
        foreach($methods as $value){
            if($value->class == $classname){
                if($access != null){
                    $methodAccess = new \ReflectionMethod($classname,$value->name);

                    switch($access){
                        case 'public':
                            if($methodAccess->isPublic())array_push($returnArr,$value->name);
                            break;
                        case 'protected':
                            if($methodAccess->isProtected())array_push($returnArr,$value->name);
                            break;
                        case 'private':
                            if($methodAccess->isPrivate())array_push($returnArr,$value->name);
                            break;
                        case 'final':
                            if($methodAccess->isFinal())$returnArr[$value->name] = 'final';
                            break;
                    }
                }else{
                    array_push($returnArr,$value->name);
                }

            }
        }
        return $returnArr;
    }

    private function getData($res){
        $title = $description =  '';
        $param = $params = $return = $returns = array();
        foreach($res as $key=>$val){
            if($key=='@title'){
                $title=$val;
            }
            if($key=='@desc'){
                $description=implode("<br>",(array)json_decode($val));
            }
            if($key=='@param'){
                $param=$val;
            }
            if($key=='@return'){
                $return=$val;
            }
        }
        //过滤传入参数
        foreach ($param as $key => $rule) {
            $rule=(array)json_decode($rule);
            $name = $rule['name'];
            if (!isset($rule['type'])) {
                $rule['type'] = 'string';
            }
            $type = isset($typeMaps[$rule['type']]) ? $typeMaps[$rule['type']] : $rule['type'];
            $require = isset($rule['required']) && $rule['required'] ? '<font color="red">必须</font>' : '可选';
            $default = isset($rule['default']) ? $rule['default'] : '';
            if ($default === NULL) {
                $default = 'NULL';
            } else if (is_array($default)) {
                $default = json_encode($default);
            } else if (!is_string($default)) {
                $default = var_export($default, true);
            }
            $desc = isset($rule['desc']) ? trim($rule['desc']) : '';
            $params[]=array('name'=>$name,'type'=>$type,'require'=>$require,'default'=>$default,'desc'=>$desc);
        }
        //过滤返回参数
        foreach ($return as $item) {
            $item=(array)json_decode($item);
            $type = $item['type'];
            $name = "";
            $required = $item['required']?'是':'否';
            $detail = $item['desc'];
            for($i=1;$i<$item['level'];$i++){
                $name .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            $name .= $item['name'];
            $returns[] = array('name'=>$name,'type'=>$type,'required'=>$required,'detail'=>$detail);
        }
        return array('title'=>$title,'desc'=>$description,'params'=>$params,'returns'=>$returns);
    }
}