<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 2:25
 */

namespace Simettric\Sense\Router;


use Simettric\Sense\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class Route {

    private $path;

    private $regexp;

    private $params=array();

    private $url_params=array();

    private $url;

    private $name;

    private $controllerClassName;

    private $actionName;

    function __construct($name,
                         $path,
                         $controllerClassName,
                         $actionName,
                         $methods=array("GET"),
                         $params=array(),
                         $requirements=array()){

        $this->name   = $name;
        $this->params = $params;
        $this->path       = $path;
        $this->controllerClassName = $controllerClassName;
        $this->actionName = $actionName;


        \preg_match_all('({\w+})', $path, $found_params);
        $found_params = isset($found_params[0]) && is_array($found_params[0]) ? $found_params[0] : array();

        $this->url_params = array();
        $regexp = $path;
        foreach($found_params as $i=>$_param){
            $_key   = str_replace(array("{","}"), "", $_param);
            $_expr = !isset($requirements[$_key]) ? '(\w+)' : $requirements[$_key];
            $regexp   = str_replace($_param, $_expr, $regexp);
            $this->url_params[$_key] = '$matches['.($i+1).']';
        }

        $this->regexp = '^' . $regexp . "$" ;

        $params = array_merge($params, $this->url_params);

        $url = "index.php?" . http_build_query($params, '', "&");
        $this->url = urldecode($url);

    }


    function getName(){
        return $this->getName();
    }

    function getPath(){
        return $this->path;
    }

    function getParams(){
        return $this->params;
    }

    function getUrlParams(){
        return $this->url_params;
    }

    function getRegExp(){
        return $this->regexp;
    }

    function getUrl(){
        return $this->url;
    }

    function getControllerClassName(){
        return $this->controllerClassName;
    }

    function getActionName(){
        return $this->actionName;
    }


} 