<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 1:04
 */

namespace Simettric\Sense\Tests\Router;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Simettric\Sense\Annotations\Route;
use Simettric\Sense\Router\RouteContainer;
use Simettric\Sense\Router\UrlGenerator;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase {


    /**
     * @var UrlGenerator
     */
    private $url_generator;

    /**
     * @var RouteContainer
     */
    private $route_container;

    private $controller_dummy = "\\Simettric\\Sense:Tests:Router:Dummy:index";

    function setUp(){

        $this->route_container = new RouteContainer();

        $this->url_generator = new UrlGenerator(
            $this->route_container,
            new DummyUrlAbsoluteGenerator()
        );

    }




    function testGenerateUrl(){

        $route = new Route();
        $route->name = "test_path";
        $route->path = "/test-path";

        $this->route_container->add($route);

        $this->assertEquals("/test-path", $this->url_generator->generateUrl("test_path"));

        $route2 = new Route();
        $route2->name = "test_params";
        $route2->path = "/test/{param}";
        $route2->configure();

        $this->route_container->add($route2);

        $this->assertEquals("test/test", $this->url_generator->generateUrl("test_params", ["param" => "test"]));


    }

    /**
     * @expectedException \Exception
     */
    function testGenerateUrlWithoutParams(){

        $route2 = new Route();
        $route2->name = "test_params";
        $route2->path = "/test/{param}";
        $route2->configure();
        $this->route_container->add($route2);

        $this->url_generator->generateUrl("test_params");

    }

    function testGenerateAbsoluteUrl(){

        $route = new Route();
        $route->name = "test_path";
        $route->path = "/test";

        $this->route_container->add($route);

        $this->assertEquals("http://example.com/es/test",
            $this->url_generator->generateUrl("test_path",
            array(),
            true)
        );


    }




}
 