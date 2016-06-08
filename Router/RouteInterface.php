<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/6/16
 * Time: 12:09
 */

namespace Simettric\Sense\Router;


interface RouteInterface {

    function configure();

    function getName();

    function getPath();

    function getParams();

    function getUrlParams();

    function getRegExp();

    function getUrl();

    function getControllerClassName();

    function getActionMethod();

    function setControllerClassName($class_name);

    function setActionMethod($method_name);

} 