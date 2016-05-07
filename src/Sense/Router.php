<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 21/04/14
 * Time: 18:20
 */

namespace Sense;


use Sense\ActionResult\AbstractActionResult;
use Sense\ActionResult\HTTPResponseActionResult;
use Sense\ActionResult\WPTemplateActionResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Router {



    private $routes=array();

    public $controller_instance, $action_name;

    private $_container;

    private $_already_matched = false;

    function __construct(Sense $sense){
        $this->_container = $sense;
    }

    function addRule($name, $path,  $params=array(),  $requirements=array()){

        $url_params = array();
        if(strpos($path, "/")===0){
            $path = substr($path, 1, strlen($path));
        }
        \preg_match_all('({\w+})', $path, $found_params);
        $found_params = isset($found_params[0]) && is_array($found_params[0]) ? $found_params[0] : array();

        $url_params = array();
        $regexp = $path;
        foreach($found_params as $i=>$_param){
            $_key   = str_replace(array("{","}"), "", $_param);
            $_expr = !isset($requirements[$_key]) ? '(\w+)' : $requirements[$_key];
            $regexp   = str_replace($_param, $_expr, $regexp);
            $url_params[$_key] = '$matches['.($i+1).']';
        }

        $regexp = '^' . $regexp . "$" ;

        $params = array_merge($params, $url_params);
        $params["__route_name"] = $name;

        $url = "index.php?" . http_build_query($params, '', "&");
        $url = urldecode($url);


        $this->routes[$name] = array("path" => $path, "regexp"=>$regexp, "url"=>$url, "params"=>$params, "url_params"=>$url_params);
    }

    function generateUrl($name, $params=array()){
        $path       = $this->routes[$name]["path"];
        $url_params = array_keys($this->routes[$name]["url_params"]);

        foreach($url_params as $required){
            if(!isset($params[$required])){

                throw new \Exception($required . " param is required for route " . $name);
            }

            //todo: match con el reg-exp


            $path = str_replace("{" .$required ."}", $params[$required], $path);
        }

        if(substr($path, 0, strlen($path)-1)=="?") str_replace("?", "", $path);


        return "/" . $path;

    }

    function init(){



        $extra = array();
        foreach ($this->routes as $name=>$route) {


            //echo $route["regexp"], $route["url"],"<br>";
            \add_rewrite_rule($route["regexp"], $route["url"],'top');

            $params = array_merge($route["params"], array_keys($route["url_params"]));

            foreach($params as $param){
                $extra[$param] = $param;
            }
        }

       // die();
//        global $wp_rewrite;
//Call flush_rules() as a method of the $wp_rewrite object
        //$wp_rewrite->flush_rules( false );

        //$routes = $this->routes;

//        add_filter('rewrite_rules_array', function($array) use($routes){
//
//                foreach ($routes as $route) {
//                    $array[$route["regexp"]] = $route["url"];
//                }
//                return $array;
//        });

        add_filter("query_vars", function($qvars) use ($extra){
            $qvars[] = '__controller';
            $qvars[] = '__action';
            $qvars[] = "__route_name";
            foreach ($extra as $param) {
                $qvars[] = $param;
            }

            return $qvars;
        });

        add_action("parse_query", array($this, "match"));

    }

    function match(){


        global $wp_query;

        remove_action("parse_query", array($this, "match"));

        if(!$wp_query->is_main_query() || $this->_already_matched) return;


        if(isset($wp_query->query_vars["__controller"]) &&
            $controller_classname = $wp_query->query_vars["__controller"]){

            $this->_already_matched = true;



            $action_name = $wp_query->query_vars["__action"] ? $wp_query->query_vars["__action"] . "Action" : "indexAction";



            $controller_classname = str_replace('\\\\','\\', $controller_classname);
            //die($controller_classname);
            //ini_set('display_errors', 1);
            $this->controller_instance  = new $controller_classname($this->_container);
            //$this->controller_instance->name = ucfirst($controller_name);
            $actionResult = $this->controller_instance->$action_name($this->_container["request"], $this->_container["wp.query"]);



            if($actionResult instanceof AbstractActionResult){

                if($actionResult instanceof WPTemplateActionResult) {

                    \add_filter('template_include', array($actionResult, "templateInclude"));

                }else{

                    /**
                     * @var $response Response
                     */
                    $response = $actionResult->getResponse();

                    if($response instanceof JsonResponse){
                        wp_send_json(json_decode($response->getContent(), true));
                    }else{

                        $response->send();


                    }

                    exit();



                }
            }





        }

    }





} 