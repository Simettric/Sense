<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 2:13
 */

namespace Simettric\Sense\Router;


use Doctrine\Common\Collections\Collection;

class UrlGenerator {


    /**
     * @var RouteContainer
     */
    private $routerContainer;

	/**
	 * @var AbsoluteUrlGeneratorInterface
	 */
	private $absoluteUrlGenerator;

    function __construct(RouteContainer $routeContainer, AbsoluteUrlGeneratorInterface $absoluteUrlGenerator){
        $this->routerContainer = $routeContainer;
	    $this->absoluteUrlGenerator = $absoluteUrlGenerator;
    }

	function setAbsoluteUrlGenerator(AbsoluteUrlGeneratorInterface $absoluteUrlGenerator){
		$this->absoluteUrlGenerator = $absoluteUrlGenerator;
	}

    function generateUrl($name, $params=array(), $absolute=false){

        if(!$route = $this->routerContainer->get($name)){
            return null;
        }

        $path       = $route->getPath();
        $url_params = array_keys($route->getUrlParams());

        foreach($url_params as $required){
            if(!isset($params[$required])){

                throw new \Exception($required . " param is required for route " . $name);
            }


            $path = str_replace("{" .$required ."}", $params[$required], $path);
        }

        if(substr($path, 0, strlen($path)-1)=="?") str_replace("?", "", $path);


        return $absolute ? $this->getAbsoluteUrl($path) : $path;

    }


	function getAbsoluteUrl($path){
		return $this->absoluteUrlGenerator->createUrl($path);
	}

} 