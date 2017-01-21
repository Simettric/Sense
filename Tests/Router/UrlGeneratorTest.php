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

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @var UrlGenerator
     */
    private $url_generator;

    /**
     * @var RouteContainer
     */
    private $route_container;

    private $controller_dummy = "\\Simettric\\Sense:Tests:Router:Dummy:index";

    public function setUp()
    {

        $this->route_container = new RouteContainer();

        $this->url_generator = new UrlGenerator(
            $this->route_container,
            new DummyUrlAbsoluteGenerator()
        );

    }




    public function testGenerateUrl()
    {

        $route = new Route();
        $route->name = "test_path";
        $route->path = "/test-path";

        $this->route_container->add($route);

        $this->assertEquals("/test-path", $this->url_generator->generateUrl("test_path"));

        $route2 = new Route();
        $route2->name = "test_params1";
        $route2->path = "/test/{param}";
        $route2->configure();

        $this->route_container->add($route2);

        $this->assertEquals("/test/test", $this->url_generator->generateUrl("test_params1", ["param" => "test"]));


    }

    /**
     * @expectedException \Exception
     */
    public function testGenerateUrlWithoutParams()
    {

        $route2 = new Route();
        $route2->name = "test_params2";
        $route2->path = "/test/{param}";
        $route2->configure();
        $this->route_container->add($route2);

        $this->url_generator->generateUrl("test_params2");

    }

    public function testGenerateAbsoluteUrl()
    {

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

    /**
     * @expectedException     \Exception
     * @expectedExceptionCode 0
     */
    public function testGenerateUrlFailWithRequirements()
    {

        $route2 = new Route();
        $route2->name = "test_params3";
        $route2->path = "/test/{param}";
        $route2->requirements = array("param"=>"\d+");
        $route2->configure();


        $this->route_container->add($route2);


        $this->url_generator->generateUrl("test_params3", array("param" => "string"));

        $this->assertEquals("/test/1", $this->url_generator->generateUrl("test_params3", array("param" => 1)));



    }

    public function testGenerateUrlWithRequirements()
    {

        $route2 = new Route();
        $route2->name = "test_params3";
        $route2->path = "/test/{param}";
        $route2->requirements = array("param"=>"\d+");
        $route2->configure();


        $this->route_container->add($route2);

        $this->assertEquals("/test/1", $this->url_generator->generateUrl("test_params3", array("param" => 1)));



    }


}
