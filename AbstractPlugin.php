<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 15/5/16
 * Time: 18:10
 */

namespace Simettric\Sense;


use Collections\Collection;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Simettric\Sense\Router\RouteInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;

abstract class AbstractPlugin {


    private $base_namespace = null;

    private $has_routes = false;

    private $rootDir = false;

    function setRootDir($dir){
        $this->rootDir = $dir;
    }

    function getConfigLocations(){
        return [ $this->rootDir . "/Config"];
    }

    function getControllerLocations(){
        return [ $this->rootDir . "/Controller"];
    }

    function getTemplateLocations(){
        return [ $this->rootDir . "/View"];
    }

    abstract function getName();


    function getBaseNamespace(){

        if(!$this->base_namespace){
            $ref = new \ReflectionObject($this);
            $this->base_namespace = $ref->getNamespaceName();
        }
        return $this->base_namespace;
    }

    function loadRoutes(Collection $routeContainer){

        AnnotationRegistry::registerFile(__DIR__ . "/../../Annotations/Route.php");

        $finder = new Finder();
        $finder->files()->in($this->getControllerLocations());
        $files = $finder->files()->name('*Controller.php');

        $this->has_routes = false;
        foreach($files as $file){


            $reader = new AnnotationReader();
            $reflClass = new \ReflectionClass($file);
            foreach($reflClass->getMethods() as $method) {

                $classAnnotations = $reader->getMethodAnnotations($method);

                /**
                 * @var $routeInterface RouteInterface
                 */
                foreach ($classAnnotations as $routeInterface) {


                    $routeInterface->setActionMethod($method->getName());
                    $routeInterface->setControllerClassName($reflClass->getName());
                    $routeInterface->configure();

                    $routeContainer->add($routeInterface);

                    if(!$this->has_routes){
                        $this->has_routes = true;
                    }

                }
            }


        }


    }




    function isTheme(){
        return false;
    }

    function hasRoutes(){
        return $this->has_routes;
    }


    function onActivate(){

        if($this->hasRoutes()){

            flush_rewrite_rules();
        }

    }

} 