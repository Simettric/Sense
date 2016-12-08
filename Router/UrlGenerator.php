<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 2:13
 */

namespace Simettric\Sense\Router;


class UrlGenerator
{


    /**
     * @var RouteContainer
     */
    private $routerContainer;

	/**
	 * @var AbsoluteUrlGeneratorInterface
	 */
	private $absoluteUrlGenerator;

    public function __construct(RouteContainer $routeContainer, AbsoluteUrlGeneratorInterface $absoluteUrlGenerator)
    {
        $this->routerContainer = $routeContainer;
	    $this->absoluteUrlGenerator = $absoluteUrlGenerator;
    }

    public function setAbsoluteUrlGenerator(AbsoluteUrlGeneratorInterface $absoluteUrlGenerator)
    {
		$this->absoluteUrlGenerator = $absoluteUrlGenerator;
	}

    public function generateUrl($name, $params=array(), $absolute=false)
    {

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
            unset($params[$required]);
        }


        if(substr($path, 0, strlen($path)-1)=="?") str_replace("?", "", $path);

        if("/" != substr($path, 0, 1))
        {
            $path = "/" . $path;
        }

        if(count($params))
        {
            $path = $path . "?" . http_build_query($params);
        }

        return $absolute ? $this->getAbsoluteUrl($path) : $path;

    }


    public function getAbsoluteUrl($path)
    {
		return $this->absoluteUrlGenerator->createUrl($path);
	}

}
