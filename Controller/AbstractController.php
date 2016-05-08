<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:42
 */

namespace Simettric\Sense\Controller;


use Simettric\Sense\ActionResult\HTTPResponseActionResult;
use Simettric\Sense\ActionResult\WPTemplateActionResult;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController {

    /**
     * @var Container
     */
    private $_container;

    function __construct(Container $container){
        $this->_container = $container;
    }

    function get($key){
        return $this->_container->get($key);
    }

    function generateUrl($url, $params=array()){
        return $this->get("router")->generateUrl($url, $params);
    }


    function resultTemplate($template, $params=array()){

        foreach($params as $name=>$param){
            $this->_container->get("view")->set($name, $param);
        }

        return new WPTemplateActionResult(
            $this->_container->getParameter("view_dir") . "/View/" .$template
        );
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



    function addScript($name, $url, $version, $deps=array(), $footer=true){
        $this->get("view")->addScript($name, $url, $version, $footer, $deps);
    }

    function addStyle($name, $url, $version, $deps=array()){
        $this->get("view")->addStyle($name, $url, $version, $deps);
    }

} 