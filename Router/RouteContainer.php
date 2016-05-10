<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 2:11
 */

namespace Simettric\Sense\Router;


use Symfony\Component\Config\Definition\Exception\Exception;

class RouteContainer {

    private $routes=array();

    private $controllerNamespacePrefixes=array();


    function getRoutes(){
        return $this->routes;
    }

    function get($name){
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    function add($name, $path,  $params=array(),  $requirements=array()){


        if(strpos($path, "/")===0){
            $path = substr($path, 1, strlen($path));
        }
        \preg_match_all('({\w+})', $path, $found_params);
        $found_params = isset($found_params[0]) && is_array($found_params[0]) ? $found_params[0] : array();

        $url_params = array();
        $regexp = $path;
        foreach($found_params as $i=>$_param){
            $_key   = str_replace(array("{","}"), "", $_param);
            $_expr = !isset($requirements[$_key]) ? '(\w+)' : $requirements[$_key];
            $regexp   = str_replace($_param, $_expr, $regexp);
            $url_params[$_key] = '$matches['.($i+1).']';
        }

        $regexp = '^' . $regexp . "$" ;

        $params = array_merge($params, $url_params);
        $params["__route_name"] = $name;

        if(isset($params["__controller"])){
            $controller_name= $this->transformControllerName($params["__controller"]);
            $params["__controller_name"] = $controller_name["controller_name"];
            $params["__action_name"]     = $controller_name["action_name"];
        }

        $url = "index.php?" . http_build_query($params, '', "&");
        $url = urldecode($url);


        $this->routes[$name] = array("path" => $path, "regexp"=>$regexp, "url"=>$url, "params"=>$params, "url_params"=>$url_params);

    }

    function transformControllerName($controllerShortName){

        $parts = explode(":", $controllerShortName);

        if(count($parts)<2){
            throw new \Exception("You need to provide a valid controller name like MyClass:index for MyClassController::indexAction");
        }

        $action_name = array_pop($parts);
        $action_name .= "Action";

        $controller_name = "";
        foreach($parts as $i=>$part){
            $controller_name .=  ($i!=0?"\\":"") . $part;
        }
        $controller_name .= "Controller";



        if(!class_exists($controller_name)){

            $exists = false;
            foreach($this->controllerNamespacePrefixes as $prefix){

                $_controller_name = $prefix . "\\" . $controller_name;
                if(class_exists($_controller_name)){
                    $controller_name = $_controller_name;
                    $exists = true;
                    break;
                }
            }

            if(!$exists)
                throw new Exception($controller_name . " does not exists");
        }

        return array(
            "controller_name" => $controller_name,
            "action_name" => $action_name
        );

    }


    function addControllerNamespacePrefix($prefix){

        if(false===is_array($prefix))
            $this->controllerNamespacePrefixes[] = $prefix;
    }


} 