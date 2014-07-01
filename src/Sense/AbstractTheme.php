<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 2/07/14
 * Time: 0:41
 */

namespace Sense;


class AbstractTheme {

    protected static $_instance;


    protected  function __construct(){}

    static function init(){
        if(!self::$_instance){
            $class = \get_called_class();
            self::$_instance = new $class;
        }
        return self::$_instance;
    }

    static function  getInstance(){
        if(!self::$_instance) self::init();

        return self::$_instance;
    }
} 