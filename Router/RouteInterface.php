<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/6/16
 * Time: 12:09
 */

namespace Simettric\Sense\Router;


use Simettric\Sense\AbstractPlugin;

interface RouteInterface {

    function configure();

    function getName();

    function getPath();

    function getParams();

    function getUrlParams();

    function getRegExp();

    function getUrl();

    function getControllerClassName();

	function setControllerClassName($class_name);

    function getActionMethod();

    function setActionMethod($method_name);

	/**
	 * @return AbstractPlugin
	 */
	function getPlugin();

	/**
	 * @param AbstractPlugin $plugin
	 *
	 * @return void
	 */
	function setPlugin(AbstractPlugin $plugin);



} 