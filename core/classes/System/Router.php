<?php namespace System;

class Router
{
    // Хранит конфигурацию маршрутов.
    public $routes = array();

    private $uri = '';
    private static $params;
    private static $rule;

    function __construct($rules = array())
    {

        $uri  = $this->getURI();
        $path = explode('/', $uri);

        $this->uri = implode('/', $path);
        if(empty($rules))
        {
            $rules =  include(Path::getRoot('app/routes.php'));
        }
        $this->routes = array('rules' => $rules);
       // $this->routes = array_reverse($this->routes);

    }

    public static function getRule()
    {
        return self::$rule;
    }

    public static function getParams()
    {
        return self::$params;
    }

    public static function getParam($parameter, $default = '')
    {
        return isset(self::$params[$parameter]) ? self::$params[$parameter] : $default;
    }

    // Метод получает URI. Несколько вариантов представлены для надёжности.
    function getURI()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }

        if (!empty($_SERVER['PATH_INFO'])) {
            return trim($_SERVER['PATH_INFO'], '/');
        }

        if (!empty($_SERVER['QUERY_STRING'])) {
            return trim($_SERVER['QUERY_STRING'], '/');
        }

        return '';
    }


    function run()
    {
        // Получаем URI.
        $uri = $this->uri;



        if (strpos($uri, '?') !== FALSE) {
            parse_str(substr($uri, strpos($uri, '?') + 1), $_GET);
            $uri = substr($uri, 0, strpos($uri, '?'));

        }


        if ($uri == '') {
            $uri = 'none';
        }
        $segments = explode('/', $uri);



        $uri          = implode('/', $segments);




        // Пытаемся применить к нему правила из конфигуации.


            foreach ($this->routes['rules'] as $pattern => $route) {


                // Если правило совпало.
                if (preg_match("~^$pattern$~", $uri) && $pattern != ' ') {



                    $internalRoute = preg_replace("~$pattern~", $route, $uri, 1);

                    $segments = explode('|', $internalRoute);

                    $controller_folder = array_shift($segments);
                    $controller_url    = array_shift($segments);


                    $controller = ucfirst($controller_url) . 'Controller';


                    $action_array = explode('-', array_shift($segments));
                    $action_name  = '';


                    foreach ($action_array as $action) {
                        $action_name .= ($action);
                    }

                    $params           = array();
                    $params['action'] = $action_name;
                    $action_name      = ucfirst($action_name);

                    if (!$action_name) {
                        $action_name = 'Index';
                    }
                    $action = 'action' . $action_name;

                    $parameters = $segments;

                    foreach ($parameters as &$parameter) {


                        $key_value = explode('=', $parameter);
                        if (isset($key_value[1])) {
                            $params[$key_value[0]] = $key_value[1];
                        } else {
                            $params[] = $key_value[0];
                        }

                    }
                    $params['controller'] = $controller_url;


                    $controller_object = $this->includeController($controller, $controller_folder);
                    self::$params      = $params;
                    self::$rule        = $pattern;

                    // Вызываем действие контроллера с параметрами
                    $controller_object->run($action, $params, $controller_url);

                    return;
                }
            }



        $controller_folder = ''; //array_shift($segments);
        $controller_url    = array_shift($segments);

        // two is controller
        $controller = ucfirst($controller_url) . 'Controller';

        // three — действие.
        $action_array = explode('-', array_shift($segments));
        $action_name  = '';
        foreach ($action_array as $action) {
            $action_name .= ($action);
        }

        $params           = array();
        $params['action'] = $action_name;
        $action_name      = ucfirst($action_name);

        if (!$action_name) {
            $action_name = 'Index';
        }

        $action     = 'action' . $action_name;
        $parameters = $segments;

        foreach ($parameters as &$parameter) {

            $key_value = explode('=', $parameter);
            if (isset($key_value[1])) {
                $params[$key_value[0]] = $key_value[1];
            } else {
                $params[] = $key_value[0];
            }

        }
        $params['controller'] = $controller_url;


        // Подключаем файл контроллера, если он имеется
        $controller_object = $this->includeController($controller, $controller_folder);

        self::$params = $params;

        // Если не загружен нужный класс контроллера или в нём нет
        // нужного метода — 404
        $controller_object->run($action, $params, $controller_url);

        // Вызываем действие контроллера с параметрами
        return;


    }

    public function includeController($controller, $controller_folder){
        return ControllerFactory::getController($controller, $controller_folder);
    }


}