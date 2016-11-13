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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;

abstract class AbstractPlugin
{


    private $base_namespace = null;

    private $has_routes = false;

    private $rootDir = false;

    public function setRootDir($dir)
    {
        $this->rootDir = $dir;
    }

    public function getConfigLocations()
    {
        return [ $this->rootDir . "/Config"];
    }

    public function getControllerLocations()
    {
        return [ $this->rootDir . "/Controller" ];
    }

    public function getTemplateLocations()
    {
        return [ $this->rootDir . "/View"];
    }

    public abstract function getName();


    public function getBaseNamespace()
    {

        if(!$this->base_namespace){
            $ref = new \ReflectionObject($this);
            $this->base_namespace = $ref->getNamespaceName();
        }
        return $this->base_namespace;
    }

    public function registerRoutes(Collection $routeContainer)
    {

        if(!count($this->getControllerLocations())) return;

    	AnnotationRegistry::registerFile(__DIR__ . "/Annotations/Route.php");

        $finder = new Finder();
        $finder->files()->in($this->getControllerLocations());
        $files = $finder->files()->name('*Controller.php');

        $this->has_routes = false;
        foreach($files as $file){

            $reader = new AnnotationReader();

	        $class = $this->getClassInFile($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename());

            $reflClass = new \ReflectionClass($class);

            foreach($reflClass->getMethods() as $method) {

                $classAnnotations = $reader->getMethodAnnotations($method);


                /**
                 * @var $routeInterface RouteInterface
                 */
                foreach ($classAnnotations as $routeInterface) {


                    $routeInterface->setActionMethod($method->getName());
                    $routeInterface->setControllerClassName($reflClass->getName());
	                $routeInterface->setPlugin($this);
                    $routeInterface->configure();

                    $routeContainer->add($routeInterface);

                    if(!$this->has_routes){
                        $this->has_routes = true;
                    }

                }
            }


        }


    }

    public function registerServices(ContainerInterface $container)
    {

	    if(count($this->getConfigLocations())){
		    $loader = new YamlFileLoader($container, new FileLocator($this->getConfigLocations()));
		    $loader->load('services.yml');
	    }

    }

    public function getClassInFile($file)
    {

		    //Grab the contents of the file
		    $contents = file_get_contents($file);

		    //Start with a blank namespace and class
		    $namespace = $class = "";

		    //Set helper values to know that we have found the namespace/class token and need to collect the string values after them
		    $getting_namespace = $getting_class = false;

		    //Go through each token and evaluate it as necessary
		    foreach (token_get_all($contents) as $token) {

			    //If this token is the namespace declaring, then flag that the next tokens will be the namespace name
			    if (is_array($token) && $token[0] == T_NAMESPACE) {
				    $getting_namespace = true;
			    }

			    //If this token is the class declaring, then flag that the next tokens will be the class name
			    if (is_array($token) && $token[0] == T_CLASS) {
				    $getting_class = true;
			    }

			    //While we're grabbing the namespace name...
			    if ($getting_namespace === true) {

				    //If the token is a string or the namespace separator...
				    if(is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {

					    //Append the token's value to the name of the namespace
					    $namespace .= $token[1];

				    }
				    else if ($token === ';') {

					    //If the token is the semicolon, then we're done with the namespace declaration
					    $getting_namespace = false;

				    }
			    }

			    //While we're grabbing the class name...
			    if ($getting_class === true) {

				    //If the token is a string, it's the name of the class
				    if(is_array($token) && $token[0] == T_STRING) {

					    //Store the token's value as the class name
					    $class = $token[1];

					    //Got what we need, stope here
					    break;
				    }
			    }
		    }

		    //Build the fully-qualified class name and return it
		    return $namespace ? $namespace . '\\' . $class : $class;


    }

	/**
	 * You can overwrite this function if you have implemented any pluggable functions
	 * https://codex.wordpress.org/Pluggable_Functions
	 *
	 * Note: Themes can´t implement pluggable functions
	 */
	public function registerPluggableFunctions()
    {
		if($this->isTheme()) return;
	}


    public function isTheme()
    {
        return false;
    }

    public function hasRoutes()
    {
        return $this->has_routes;
    }


    public function onActivate()
    {

        if($this->hasRoutes()){

            flush_rewrite_rules();
        }

    }

}
