<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 2:11
 */

namespace Simettric\Sense\Router;


use Collections\Collection;
use Symfony\Component\Config\Definition\Exception\Exception;

class RouteContainer {

    private $routes=array();

    private $controllerNamespacePrefixes=array();


    function getRoutes(){
        return $this->routes;
    }

    /**
     * @param $name
     * @return Route|null
     */
    function get($name){
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    function add($name, $path, $params=array(), $methods=array("GET"), $requirements=array()){


        $controller_name= $this->transformControllerName($params["__controller"]);
        unset($params["__controller"]);
        $params["__route_name"] = $name;

        $this->routes[$name] = new Route(
            $name,
            $path,
            $controller_name["controller_name"],
            $controller_name["action_name"],
            $methods,
            $params,
            $requirements
        );

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