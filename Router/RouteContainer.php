<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 2:11
 */

namespace Simettric\Sense\Router;


class RouteContainer {

    private $routes;


    function getRoutes(){
        return $this->routes;
    }

    function get($name){
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    function addRoute($name, $path,  $params=array(),  $requirements=array()){


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

        $url = "index.php?" . http_build_query($params, '', "&");
        $url = urldecode($url);


        $this->routes[$name] = array("path" => $path, "regexp"=>$regexp, "url"=>$url, "params"=>$params, "url_params"=>$url_params);

    }


} 