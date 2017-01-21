<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 1:44
 */

namespace Simettric\Sense\Tests\Router;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\IndexedReader;
use Simettric\Sense\Annotations\Route;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;

class RouteAnnotationsTest extends \PHPUnit_Framework_TestCase
{

    public function testReadAnnotationsInFile()
    {

        AnnotationRegistry::registerFile(__DIR__ . "/../../Annotations/Route.php");
        $reader = new AnnotationReader();
        $reflClass = new \ReflectionClass("Simettric\\Sense\\Tests\\Router\\DummyController");


        foreach($reflClass->getMethods() as $method)
        {

            if($method->getName() == "fakeAction")
            {

                $classAnnotations = $reader->getMethodAnnotations($method);

                $annotation       = $classAnnotations[0];
                $this->assertInstanceOf(get_class(new Route()), $annotation);
                $this->assertNotNull($annotation->path);
                $this->assertNotNull($annotation->name);
                $this->assertEquals("\w+", $annotation->requirements["test_route"]);

                $this->assertEquals("\d+", $annotation->requirements["id"], "falla para método " . $method->getName() );
            }




        }


    }

    public function testFindAnnotationClasses()
    {
        AnnotationRegistry::registerFile(__DIR__ . "/../../Annotations/Route.php");


        $finder = new Finder();
        $finder->files()->in(__DIR__);
        $files = $finder->files()->name('*Controller.php');

        $this->has_routes = false;
        foreach($files as $file){


            $reader = new AnnotationReader();
            $reflClass = new \ReflectionClass($file);
            foreach($reflClass->getMethods() as $method) {

                $classAnnotations = $reader->getMethodAnnotations($method);
                foreach($classAnnotations as $annotation ){

                    $this->assertInstanceOf(get_class(new Route()), $annotation);
                    $this->assertNotNull($annotation->path);
                    $this->assertNotNull($annotation->name);

                }

            }


        }


    }


}
