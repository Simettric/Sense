<?php
/**
 *
 *
 * @author Asier Marqués <asiermarques@gmail.com>
 */

namespace Simettric\Sense\Admin;


use Simettric\Sense\ActionResult\ActionResultInterface;
use Simettric\Sense\ActionResult\HTTPResponseActionResult;
use Simettric\Sense\Controller\AbstractAdminController;
use Simettric\Sense\Exception\NotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Simettric\Sense\Annotations\AdminRoute;

class Router
{

    /**
     * @var RouteContainer
     */
    private $routeContainer;

    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    function __construct(RouteContainer $container, ContainerInterface $serviceContainer)
    {

        $this->routeContainer = $container;
        $this->serviceContainer = $serviceContainer;

    }

    public function registerRouteRules()
    {

        $self = $this;

        /**
         * @var $route AdminRoute
         */
        foreach ($this->routeContainer as $route)
        {

            $class = $route->getControllerClassName();
            $controller = new $class($this->serviceContainer, $route->getPlugin());
            $method     = $route->getActionMethod();

            add_menu_page(
                $route->page_title,
                $route->menu_title,
                $route->capability,
                $route->path,
                function () use ($controller, $method, $self) {

                    $self->executeController($controller, $method);
                },
                $route->icon_url,
                $route->position
            );

        }

    }

    function executeController(AbstractAdminController $controller, $method)
    {

        /**
         * @var $wp_query \WP_Query
         */
        global $wp_query;

        try{

            $result = call_user_func(array($controller, $method), $this->serviceContainer->get("request"), $wp_query );

            if($result instanceof ActionResultInterface)
            {
                $result->execute();
                return;
            }

            echo $result;

        }catch (NotFoundException $e)
        {
            $wp_query->set_404();
            status_header(404);
        }




    }

}