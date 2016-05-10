<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 1:44
 */

namespace Simettric\Sense\Tests\Router;


use Simettric\Sense\Router\RouteContainer;

class RouteContainerTest extends \PHPUnit_Framework_TestCase {


    function testTransformControllerName(){
        $container = new RouteContainer();
        $this->assertEquals(array(
                "controller_name" => "Simettric\\Sense\\Tests\\Router\\DummyController",
                "action_name"     => "indexAction"
            ),
            $container->transformControllerName("Simettric:Sense:Tests:Router:Dummy:index")
        );
    }

    /**
     * @expectedException \Exception
     */
    function testControllerNameNotExists(){
        $container = new RouteContainer();

        $container->transformControllerName("Simettric:Sense:Tests:Dummy:index");

    }

    function testControllerNamePrefix(){
        $container = new RouteContainer();
        $container->addControllerNamespacePrefix("Simettric\\Sense\\Tests\\Router");

        $this->assertEquals(array(
                "controller_name" => "Simettric\\Sense\\Tests\\Router\\DummyController",
                "action_name"     => "indexAction"
            ),
            $container->transformControllerName("Dummy:index")
        );

    }
}
 