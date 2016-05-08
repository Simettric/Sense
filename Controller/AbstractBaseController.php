<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 15:04
 */

namespace Simettric\Sense\Controller;


use Symfony\Component\DependencyInjection\Container;

abstract class AbstractBaseController {

	/**
	 * @var Container
	 */
	protected $container;


	function __construct(Container $container){
		$this->container = $container;
	}

	function get($key){
		return $this->container->get($key);
	}

	function addScript($name, $url, $version, $deps=array(), $footer=true){
		$this->container->get("view")->addScript($name, $url, $version, $footer, $deps);
	}

	function addStyle($name, $url, $version, $deps=array()){
		$this->container->get("view")->addStyle($name, $url, $version, $deps);
	}


	function assignToView($key, $value){
		$this->container->get("view")->set($key, $value);
	}


	function generateUrl($url, $params=array()){
		return $this->container->get("router")->generateUrl($url, $params);
	}


} 