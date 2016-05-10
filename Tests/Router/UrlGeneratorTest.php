<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 1:04
 */

namespace Simettric\Sense\Tests\Router;


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

    function setUp(){

        $this->route_container = new RouteContainer();
        $this->url_generator = new UrlGenerator(
            $this->route_container,
            new DummyUrlAbsoluteGenerator()
        );

    }


    function testGenerateUrl(){

        $this->route_container->add("test_path", "/test-path");

        $this->assertEquals("/test-path", $this->url_generator->generateUrl("test_path"));

        $this->route_container->add("test_params", "/test/{param}");

        $this->assertEquals("/test/test", $this->url_generator->generateUrl("test_params", ["param" => "test"]));


    }

    /**
     * @expectedException \Exception
     */
    function testGenerateUrlWithoutParams(){

        $this->route_container->add("test_params", "/test/{param}");

        $this->url_generator->generateUrl("test_params");

    }

    function testGenerateAbsoluteUrl(){

        $this->route_container->add("test_path", "/test");

        $this->assertEquals("http://example.com/es/test",
            $this->url_generator->generateUrl("test_path",
            array(),
            true)
        );


    }




}
 