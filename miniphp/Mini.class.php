<?php

if (!defined('IN_MINI')) {
    exit();
}

error_reporting(E_ALL^E_NOTICE);

//系统目录定义
defined('CONTROLLER_DIR')  or define('CONTROLLER_DIR',  'application/controllers'.DIRECTORY_SEPARATOR);
defined('MODEL_DIR')       or define('MODEL_DIR',       'application/models' . DIRECTORY_SEPARATOR);


// 系统常量定义
defined('DEFAULT_CONTROLLER')  or define('DEFAULT_CONTROLLER',  'Index');
defined('DEFAULT_ACTION')      or define('DEFAULT_ACTION',      'index');

class mini {

    public static $controller;
    public static $action;

    public static function run(){
        $url_params = self::getUrlInfo();

        self::$controller = $url_params['controller'];
        self::$action     = $url_params['action'];

        //通过实例化及调用所实例化对象的方法,来完成controller中action页面的加载
        $controller = self::$controller . 'Controller';
        $action     = self::$action . 'Action';

        //加载当前要运行的controller文件
        if (is_file(CONTROLLER_DIR . $controller . '.class.php')) {
            //当文件在controller根目录下存在时,直接加载.
            require_once CONTROLLER_DIR . $controller . '.class.php';
        }else{
            self::display404Error();
        }

        $controller = new $controller();

        if(method_exists($controller, $action)){
            $controller->$action();
        }else{
            self::display404Error();
        }
    }

    public static function display404Error(){
        exit('404');
    }

    public static function getUrlInfo(){
        //分析包含路由信息的网址
        if (isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['REQUEST_URI'])) {
            //当项目开启Rewrite设置时
            if (DOIT_REWRITE === false) {
                $pathUrlString = strlen($_SERVER['SCRIPT_NAME']) > strlen($_SERVER['REQUEST_URI']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_URI'];
                $pathUrlString = str_replace($_SERVER['SCRIPT_NAME'], '', $pathUrlString);
            } else {
                $pathUrlString = str_replace(str_replace('/' . ENTRY_SCRIPT_NAME, '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']);
                //去掉伪静态网址后缀
                $pathUrlString = str_replace(URL_SUFFIX, '', $pathUrlString);
            }

            //如网址(URL)含有'?'(问号),则过滤掉问号(?)及其后面的所有字符串
            $pos = strpos($pathUrlString, '?');
            if ($pos !== false) {
                $pathUrlString = substr($pathUrlString, 0, $pos);
            }

            //将处理过后的有效URL进行分析,提取有用数据.
            $urlInfoArray = explode(URL_SEGEMENTATION, str_replace('/', URL_SEGEMENTATION, $pathUrlString));

            //获取 controller名称
            $controllerName  = (isset($urlInfoArray[1]) && $urlInfoArray[1] == true) ? $urlInfoArray[1] : DEFAULT_CONTROLLER;

            //获取 action名称
            $actionName  = (isset($urlInfoArray[2]) && $urlInfoArray[2] == true) ? $urlInfoArray[2] : DEFAULT_ACTION;

            //变量重组,将网址(URL)中的参数变量及其值赋值到$_GET全局超级变量数组中
            if (($totalNum = sizeof($urlInfoArray)) > 4) {
                for ($i = 3; $i < $totalNum; $i += 2) {
                    if (!$urlInfoArray[$i]) {
                        continue;
                    }
                    $_GET[$urlInfoArray[$i]] = $urlInfoArray[$i + 1];
                }
            }
            //删除不必要的变量,清空内存占用
            unset($urlInfoArray);

            return array('controller' => ucfirst(strtolower($controllerName)), 'action' => strtolower($actionName));

        }

        return array('controller' => DEFAULT_CONTROLLER, 'action' => DEFAULT_ACTION);
    }

}



//spl_autoload_register(array('AutoLoad', 'index'));

