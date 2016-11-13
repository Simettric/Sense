<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 3:26
 */

namespace Simettric\Sense\Tests\Router;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Simettric\Sense\ActionResult\ActionResultInterface;
use Simettric\Sense\Router\Route;
use Simettric\Sense\Router\RouteContainer;
use Simettric\Sense\Router\Router;
use Symfony\Component\DependencyInjection\Container;

class RouterTest extends \PHPUnit_Framework_TestCase
{


    public function getRoute()
    {

        AnnotationRegistry::registerFile(__DIR__ . "/../../Annotations/Route.php");
        $reader = new AnnotationReader();
        $reflClass = new \ReflectionClass("Simettric\\Sense\\Tests\\Router\\DummyController");

        $route = null;
        foreach($reflClass->getMethods() as $method){

            $classAnnotations = $reader->getMethodAnnotations($method);
            foreach($classAnnotations as $annotation ){

                $route = $annotation;
                $route->setActionMethod($method->getName());
                $route->setControllerClassName($reflClass->getName());
                $route->configure();
                break 2;
            }

        }
        return $route;
    }

    public function testExecuteController()
    {

        $di_container = $this->getMockBuilder(Container::class)->getMock();
        $di_container->expects($this->any())
            ->method('get')
            ->will($this->returnValue(null));





        $router = new Router($di_container);

        $this->assertInstanceOf(ActionResultInterface::class, $router->executeControllerAction($this->getRoute()));
        

    }

}
