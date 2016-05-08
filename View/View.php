<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 14:49
 */

namespace Simettric\Sense\View;


use Simettric\Sense\Traits\ArrayTrait;

class View {

	use ArrayTrait;

	private $styles = array();

	private $javascripts = array();

	private $parameters = array();


	function get($key, $default=null){
		return $this->getArrayValue($key, $this->parameters, $default);
	}

	function set($key, $value){
		$this->parameters[$key] = $value;
	}


	function addStyle($key, $url, $version, $dependencies=array(), $media="all"){
		$this->styles[$key] = array(
			'src'       => $url,
			'dependencies'  => $dependencies,
			'version'   => $version,
			'media'     => $media
		);
	}

	function addScript($key, $url, $version, $in_footer=true, $dependencies=array()){
		$this->javascripts[$key] = array(
			'src'       => $url,
			'dependencies'  => $dependencies,
			'version'   => $version,
			'in_footer' => $in_footer
		);
	}


	function onEnqueueScriptsAction(){
		foreach($this->javascripts as $handle=>$item){
			if($item["src"]){
				\wp_enqueue_script( $handle, $item["src"], $item["dependencies"], $item["version"], $item["in_footer"] );
			}else{
				\wp_enqueue_script( $handle );
			}
		}
		foreach($this->styles as $handle=>$item){
			if($item["src"]){
				\wp_enqueue_style( $handle, $item["src"], $item["dependencies"], $item["version"], $item["media"] );
			}else{
				\wp_enqueue_style( $handle );
			}
		}
	}
} 