<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 15/5/16
 * Time: 18:10
 */

namespace Simettric\Sense;


use Simettric\Sense\Router\RouteContainer;
use Symfony\Component\Config\FileLocator;

abstract class AbstractPlugin {


    private $has_routes = false;

    abstract function getConfigLocations();

    abstract function getTemplateLocations();

    abstract function getName();


    function loadRoutes(RouteContainer $routeContainer){
        $locator = new FileLocator($this->getConfigLocations());
        $routes = $locator->locate('routes.yml', null, false);
        foreach($routes as $route){
            $routeContainer->add(
              $route["name"],
              $route["path"],
              $route["params"],
              $route["methods"],
              $route["requirements"]
            );
        }

        $this->has_routes = (bool) count($routes);
    }




    function isTheme(){
        return false;
    }

    function hasRoutes(){
        return $this->has_routes;
    }


    function onActivate(){

        if($this->hasRoutes()){

            flush_rewrite_rules();
        }

    }

} 