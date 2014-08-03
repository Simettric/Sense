<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 2/07/14
 * Time: 0:41
 */

namespace Sense;


abstract class AbstractTheme {


    static $_instance;

    protected $_theme_vars=array();

    /**
     * @var Router
     */
    private $_router;

    private function __construct(){}


    static function getInstance(){
        if(!self::$_instance){
            $class = \get_called_class();
            self::$_instance = new $class;
        }
        return self::$_instance;
    }


    abstract function setUp();


    /**
     * @param Router $router
     */
    function setRouter(Router $router){
        $this->_router = $router;
    }

    function genUrl($name){
        return $this->_router->generateUrl($name);
    }



    function getMeta($key, $post_id=null, $single=true){
        if(!$post_id)  $post_id = \get_the_ID();

        return \get_post_meta($post_id, $key, $single);

    }

    function getDir() {
        $rc = new \ReflectionClass(\get_class($this));
        return dirname($rc->getFileName());
    }

    function assign($key, $variable){
        $this->_theme_vars[$key] = $variable;
    }

    function get($key){
        return isset($this->_theme_vars[$key]) ? $this->_theme_vars[$key] : null;//TODO excepcion
    }
} 