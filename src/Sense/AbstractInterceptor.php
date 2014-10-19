<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 26/07/14
 * Time: 17:41
 */

namespace Sense;


use Sense\Interfaces\Interceptor;

abstract class AbstractInterceptor implements Interceptor {


    protected static $executed = false;

    private $_sense;

    function __construct(Sense $sense){
        $this->_sense = $sense;


    }


    function isExecuted(){
        $executed = static::$executed;

        static::$executed = true;

        return $executed;
    }

    function get($key){
        return $this->_sense[$key];
    }

    function assignToView($key, $value){
        $this->_sense["theme"]->assign($key, $value);
    }

    function addScript($name, $url, $version, $deps=array(), $footer=true){
        $this->get("sense.theme_assets")->addScript($name, $url, $version, $footer, $deps);
    }

    function addStyle($name, $url, $version, $deps=array()){
        $this->get("sense.theme_assets")->addStyle($name, $url, $version, $deps);
    }


}