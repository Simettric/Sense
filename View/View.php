<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 14:49
 */

namespace Simettric\Sense\View;


use Simettric\Sense\Router\UrlGenerator;
use Simettric\Sense\Traits\ArrayTrait;

class View
{

	use ArrayTrait;


	private $parameters = array();

	/**
	 * @var UrlGenerator
	 */
	private $urlGenerator;


    public function __construct(UrlGenerator $urlGenerator)
    {
		$this->urlGenerator = $urlGenerator;
	}


    public function get($key, $default=null)
    {
		return $this->getArrayValue($key, $this->parameters, $default);
	}

    public function set($key, $value)
    {
		$this->parameters[$key] = $value;
	}


    public function path($name, $params)
    {
		return $this->urlGenerator->generateUrl($name, $params);
	}

    public function url($name, $params)
    {
		return $this->urlGenerator->generateUrl($name, $params, true);
	}

}
