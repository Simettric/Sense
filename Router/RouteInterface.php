<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/6/16
 * Time: 12:09
 */

namespace Simettric\Sense\Router;


use Simettric\Sense\AbstractPlugin;

interface RouteInterface
{

    public function configure();

    public function getName();

    public function getPath();

    public function getParams();

    public function getUrlParams();

    public function getRegExp();

    public function getUrl();

    public function getControllerClassName();

    public function setControllerClassName($class_name);

    public function getHTTPMethod();

    public function getActionMethod();

    public function setActionMethod($method_name);

	/**
	 * @return AbstractPlugin
	 */
    public function getPlugin();

	/**
	 * @param AbstractPlugin $plugin
	 *
	 * @return void
	 */
    public function setPlugin(AbstractPlugin $plugin);

}
