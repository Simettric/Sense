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
use Simettric\Sense\Annotations\AdminRoute;
use Simettric\Sense\Annotations\Route;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;

class AdminRouteAnnotationsTest extends \PHPUnit_Framework_TestCase
{

    public function testReadAnnotationsInFile()
    {

        AnnotationRegistry::registerFile(__DIR__ . "/../../Annotations/AdminRoute.php");
        $reader = new AnnotationReader();
        $reflClass = new \ReflectionClass( DummyAdminController::class );


        foreach($reflClass->getMethods() as $method)

        {
            if($method->getName() == "fakeAdminAction")
            {

                $classAnnotations = $reader->getMethodAnnotations($method);

                $annotation = $classAnnotations[0];

                $this->assertInstanceOf( AdminRoute::class, $annotation);

                $this->assertEquals( '/demo/', $annotation->path );
                $this->assertEquals( 'name', $annotation->name );
                $this->assertEquals( 'page_title', $annotation->page_title );
                $this->assertEquals( 'menu_title', $annotation->menu_title );
                $this->assertEquals( 'capability', $annotation->capability );
                $this->assertEquals( 'icon_url', $annotation->icon_url );
                $this->assertEquals( 1 , $annotation->position );

            }

        }

    }
    public function testReadAnnotationsDefaultValuesInFile()
    {

        AnnotationRegistry::registerFile(__DIR__ . "/../../Annotations/AdminRoute.php");
        $reader = new AnnotationReader();
        $reflClass = new \ReflectionClass( DummyDefaultAdminController::class );


        foreach($reflClass->getMethods() as $method)

        {
            if($method->getName() == "fakeAdminAction")
            {

                $classAnnotations = $reader->getMethodAnnotations($method);

                $annotation = $classAnnotations[0];

                $annotation->configure();

                $this->assertInstanceOf( AdminRoute::class, $annotation);

                $this->assertEquals(  $annotation->name , $annotation->page_title );
                $this->assertEquals(  $annotation->name , $annotation->menu_title );
                $this->assertEquals( 'manage_options', $annotation->capability );
                $this->assertEquals( null , $annotation->icon_url );
                $this->assertEquals( 0 , $annotation->position );

            }


        }


    }


}
