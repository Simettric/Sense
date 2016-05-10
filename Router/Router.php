<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 2:00
 */

namespace Simettric\Sense\Router;


use Simettric\Sense\ActionResult\ActionResultInterface;
use Simettric\Sense\ActionResult\WPTemplateActionResult;
use Simettric\Sense\Traits\ArrayTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Router {


    use ArrayTrait;

    public $controller_instance, $action_name;

    /**
     * @var RouteContainer
     */
    private $routeContainer;

    /**
     * @var Container
     */
    private $_container;

    private $_already_matched = false;

    function __construct(RouteContainer $routeContainer, Container $container){
        $this->_container = $container;
        $this->routeContainer = $routeContainer;
    }


    function registerRouteRules(){

        $extra = array();
        foreach ($this->routeContainer->getRoutes() as $name=>$route) {


            \add_rewrite_rule($route["regexp"], $route["url"],'top');

            $params = array_merge($route["params"], array_keys($route["url_params"]));

            foreach($params as $param){
                $extra[$param] = $param;
            }

        }


        \add_filter("query_vars", function($qvars) use ($extra){
            $qvars[] = "__route_name";
            foreach ($extra as $param) {
                $qvars[] = $param;
            }

            return $qvars;
        });



    }

    function match(){


        global $wp_query;

        \remove_action("parse_query", array($this, "match"));

        if(!$wp_query->is_main_query() || $this->_already_matched) return;


        $route_name = $this->getArrayValue("__route_name", $wp_query->query_vars);

        /**
         * @var $route Route
         */
        if( $route = $this->routeContainer->get($route_name) ){

            $this->_already_matched = true;

            $actionResult = $this->executeControllerAction($route);

            if($actionResult instanceof ActionResultInterface){

                $actionResult->execute();

            }else{

                throw new \Exception($route->getControllerClassName() . "::" . $route->getActionName() . " must to return an ActionResult object");

            }

        }

    }

    /**
     * @param Route $route
     * @return ActionResultInterface
     * @throws \Exception
     */
    function executeControllerAction(Route $route){

        $controller_name = $route->getControllerClassName();
        $action_name     = $route->getActionName();
        return call_user_func(
                    [new $controller_name($this->_container), $action_name],
                    $this->_container->get("request"),
                    $this->_container->get("wp.query")
        );

    }


} 