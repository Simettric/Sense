<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 27/06/14
 * Time: 19:14
 */

namespace Sense;


use Sense\ActionResult\HTTPResponseActionResult;
use Sense\ActionResult\WPTemplateActionResult;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class AbstractController
 * @package Sense
 *
 *
 * Never call $query->get_posts in a controller if the query_vars were altered
 */
abstract class AbstractController {

    private $_container;

    function __construct(Sense $sense){
        $this->_container = $sense;
    }

    function generateUrl($url, $params=array()){
        return $this->get("router")->generateUrl($url, $params);
    }


    function resultTemplate($template, $params=array()){

        foreach($params as $name=>$param){
            $this->getTheme()->assign($name, $param);
        }

        return new WPTemplateActionResult(array(
            "template" => $this->_container["%sense.app_dir%"] . "/View/" .$template
        ));
    }

    function resultResponse($content, $code=200, $headers=array()){


        if($content instanceof Response){
            $response = $content;
        }else{
            $response = new Response($content, $code, $headers);
        }


        $result =  new HTTPResponseActionResult();
        $result->setResponse($response);

        return $result;

    }

    function resultRedirect($url, $code=302, $headers=array()){

        $response = new RedirectResponse($url, $code, $headers);

        $result =  new HTTPResponseActionResult();
        $result->setResponse($response);

        return $result;

    }

    /**
     * @return AbstractTheme
     */
    function getTheme(){
        return $this->get("theme");
    }


    function get($key){
        return $this->_container[$key];
    }


    function addScript($name, $url, $version, $deps=array(), $footer=true){
        $this->get("sense.theme_assets")->addScript($name, $url, $version, $footer, $deps);
    }

    function addStyle($name, $url, $version, $deps=array()){
        $this->get("sense.theme_assets")->addStyle($name, $url, $version, $deps);
    }






} 