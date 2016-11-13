<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 2:00
 */

namespace Simettric\Sense\Router;


use Collections\Collection;
use Simettric\Sense\ActionResult\ActionResultInterface;
use Simettric\Sense\ActionResult\WPTemplateActionResult;
use Simettric\Sense\Traits\ArrayTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{


    use ArrayTrait;

    public $controller_instance, $action_name;

    /**
     * @var RouteContainer
     */
    private $_routeContainer;

    /**
     * @var Request
     */
    private $_request;

    /**
     * @var ContainerInterface
     */
    private $_container;

    private $_already_matched = false;

    public function __construct(RouteContainer $routeContainer, Request $request, ContainerInterface $container)
    {
        $this->_routeContainer = $routeContainer;
        $this->_request        = $request;
        $this->_container      = $container;
    }

    public function registerRouteRules()
    {

        $extra = array();

        /**
         * @var $route RouteInterface
         */
        foreach ($this->_routeContainer as $route) {

	        $params = array_merge(array_keys($route->getParams()), array_keys($route->getUrlParams()));

	        foreach ($params as $param) {
		        add_rewrite_tag('%' .$param. '%', '([^&]+)');
	        }

            \add_rewrite_rule($route->getRegExp(), $route->getUrl(), 'top');

        }



        \add_filter("query_vars", function($qvars) use ($extra){

            foreach ($extra as $param) {
                $qvars[] = $param;
            }

            return $qvars;
        });



    }

    public function match(\WP_Query $wp_query)
    {

        \remove_action("parse_query", array($this, "match"));

        if(!$wp_query->is_main_query() || $this->_already_matched) return;

        $route_name = $this->getArrayValue("__route_name", $wp_query->query_vars);

        $route      = $this->_routeContainer->get($route_name);


        /**
         * @var $route RouteInterface
         */
        if( $route ){

            $this->_already_matched = true;

            $actionResult = $this->executeControllerAction($route, $wp_query);

            if($actionResult instanceof ActionResultInterface){

                $actionResult->execute();

            }else{

                throw new \Exception($route->getControllerClassName() . "::" . $route->getActionMethod() . " must to return an ActionResult object");

            }

        }

    }

    public function regenerateWPRouteCache()
    {
    	flush_rewrite_rules(true);
    }

    /**
     * @param RouteInterface $route
     * @return ActionResultInterface
     * @throws \Exception
     */
    public function executeControllerAction(RouteInterface $route, \WP_Query $wp_query)
    {

        $controller_name = $route->getControllerClassName();
        $action_name     = $route->getActionMethod();




        if($this->_request)
        {
            foreach ($route->getUrlParams() as $param_name=>$param_match)
            {
                $this->_request->attributes->set($param_name, $wp_query->query_vars[$param_name]);
            }
        }




        return call_user_func(
                    [new $controller_name($this->_container, $route->getPlugin()), $action_name],
                    $this->_request,
                    $wp_query
        );

    }


}
