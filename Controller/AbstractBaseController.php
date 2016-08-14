<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 15:04
 */

namespace Simettric\Sense\Controller;


use Simettric\Sense\AbstractPlugin;
use Symfony\Component\DependencyInjection\Container;

abstract class AbstractBaseController {

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var AbstractPlugin
	 */
	protected $plugin;


	function __construct(Container $container, AbstractPlugin $plugin=null){
		$this->container = $container;
		$this->plugin = $plugin;
	}

	function get($key){
		return $this->container->get($key);
	}

	function addScript($name, $url, $version, $deps=array(), $footer=true){
		$this->container->get("view_assets")->addScript($name, $url, $version, $footer, $deps);
	}

	function addStyle($name, $url, $version, $deps=array()){
		$this->container->get("view_assets")->addStyle($name, $url, $version, $deps);
	}


	function assignToView($key, $value){
		$this->container->get("view")->set($key, $value);
	}


	function generateUrl($url, $params=array(), $absolute=false){
		return $this->container->get("router")->generateUrl($url, $params, $absolute);
	}


} 