<?php
/**
 * Created by PhpStorm.
 * User: momo
 * Date: 2017/8/28
 * Time: 下午4:18
 */

namespace Reflection\Api\Doc;

class Documents
{
    /**
     * 接口的列表页
     * @author lz
     * @date 2016-6-15
     */
    public function index(){
        $filename=scandir(YIN_PATH."/controller");
        $result = $data = array();
        foreach($filename as $val) {
            if($val != '.' && $val != '..' && $val != 'DocController.php'&& $val != 'IndexController.php'){
                $val = substr($val,0,strrpos($val,'.'));
                $methods = $this->getMethods("controller\\$val",'public');
                foreach($methods as $k=>$v){
                    $meth_v['name'] = $v;
                    $meth_v['title'] = $this->Mtitle("controller\\$val",$v);
                    $methods[$k] = $meth_v;
                }
                $data['title'] = $this->Ctitle("controller\\$val");
                $data['class'] = $val;
                $data['method'] = $methods;
                $result[] = $data;
            }
        }
        $this->assign('list',$result);
        $this->display('doc/index.html');
    }

    /**
     * 获取接口名称
     * @param $class
     * @param $method
     * @return mixed
     */
    public function Mtitle($class,$method){
        $re = new Reflection($class);
        $res = $re->getMethod($method);
        $item = $this->getData($res);
        return $item['title'];
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
     * 处理每个接口的数据
     * @author lz
     * @date 2016-6-15
     */
    public function detail(){
        $class = $this->get_gp('class','G');
        $method = $this->get_gp('method','G');

        $re = new Reflection("controller\\$class");
        $res = $re->getMethod($method);
        $item = $this->getData($res);
        $this->assign('url',Yin::url('doc|index'));
        $this->assign('title',$item['title']);
        $this->assign('desc',$item['desc']);
        $this->assign('params',$item['params']);
        $this->assign('return',$item['returns']);
        $this->display('doc/article.html');

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