<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 2:13
 */

namespace Simettric\Sense\Router;


class UrlGenerator {

    /**
     * @var RouteContainer
     */
    private $routerContainer;

    function __construct(RouteContainer $routeContainer){
        $this->routerContainer = $routeContainer;
    }

    function generateUrl($name, $params=array()){

        if(!$route = $this->routerContainer->get($name)){
            return null;
        }

        $path       = $route["path"];
        $url_params = array_keys($route["url_params"]);

        foreach($url_params as $required){
            if(!isset($params[$required])){

                throw new \Exception($required . " param is required for route " . $name);
            }


            $path = str_replace("{" .$required ."}", $params[$required], $path);
        }

        if(substr($path, 0, strlen($path)-1)=="?") str_replace("?", "", $path);


        return "/" . $path;

    }

} 