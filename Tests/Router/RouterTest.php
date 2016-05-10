<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 3:26
 */

namespace Simettric\Sense\Tests\Router;


use Simettric\Sense\ActionResult\ActionResultInterface;
use Simettric\Sense\Router\Route;
use Simettric\Sense\Router\RouteContainer;
use Simettric\Sense\Router\Router;
use Symfony\Component\DependencyInjection\Container;

class RouterTest extends \PHPUnit_Framework_TestCase {


    function testExecuteController(){

        $di_container = $this->getMockBuilder(Container::class)->getMock();
        $di_container->expects($this->any())
            ->method('get')
            ->will($this->returnValue(null));

        $container = new RouteContainer();
        $container->addControllerNamespacePrefix("Simettric\\Sense\\Tests\\Router");

        $name_parts = $container->transformControllerName("Dummy:fake");

        $route = new Route("test", "/test", $name_parts["controller_name"], $name_parts["action_name"]);
        $router = new Router($container, $di_container);


        $this->assertInstanceOf(ActionResultInterface::class, $router->executeControllerAction($route));
        

    }

}
 