<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 2/07/14
 * Time: 0:41
 */

namespace Sense;


use Sense\Model\UserModel;

abstract class AbstractTheme {


    static $_instance;

    protected $_theme_vars=array();

    /**
     * @var Router
     */
    private $_router;

    /**
     * @var UserModel
     */
    private $_userModel;

    /**
     * @var Util
     */
    protected  $_util;

    private function __construct(){}


    static function getInstance(){
        if(!self::$_instance){
            $class = \get_called_class();
            self::$_instance = new $class;
        }
        return self::$_instance;
    }


    abstract function setUp();



    function setUserModel(UserModel $model){
        $this->_userModel = $model;
    }


    /**
     * @param Router $router
     */
    function setRouter(Router $router){
        $this->_router = $router;
    }

    function genUrl($name, $params=array(), $absolute=false){
        return ($absolute ? home_url( '' ) . $this->_router->generateUrl($name, $params) : '' . $this->_router->generateUrl($name, $params));
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

    function getUserModel(){
        return $this->_userModel;
    }

    function setUtil(Util $util){
        $this->_util = $util;
    }

    /**
     * @param \WP_Query $query
     * @return array|string
     */
    function paginateLinks(\WP_Query $query=null){

        if(!$query){
            $query = $this->get("wp.query");
        }


        $constant = 999999999999999;
        return \paginate_links(array(
            'base' => str_replace( $constant, '%#%', esc_url( get_pagenum_link( $constant ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, $query->query_vars["paged"]),
            'total' => $query->max_num_pages
        ));

    }
} 