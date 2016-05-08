<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 14:49
 */

namespace Simettric\Sense\View;


use Simettric\Sense\Router\UrlGenerator;
use Simettric\Sense\Traits\ArrayTrait;

class View {

	use ArrayTrait;


	private $parameters = array();

	/**
	 * @var UrlGenerator
	 */
	private $urlGenerator;


	function __construct(UrlGenerator $urlGenerator){
		$this->urlGenerator = $urlGenerator;
	}


	function get($key, $default=null){
		return $this->getArrayValue($key, $this->parameters, $default);
	}

	function set($key, $value){
		$this->parameters[$key] = $value;
	}


	function path($name, $params){
		return $this->urlGenerator->generateUrl($name, $params);
	}

	function url($name, $params){
		return $this->urlGenerator->generateUrl($name, $params, true);
	}


} 