<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:42
 */

namespace Simettric\Sense\Controller;


use Simettric\Sense\AbstractPlugin;
use Simettric\Sense\ActionResult\HTTPResponseActionResult;
use Simettric\Sense\ActionResult\WPTemplateActionResult;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController  {

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var AbstractPlugin
     */
    protected $plugin;


    function __construct(Container $container, AbstractPlugin $plugin=null){
        $this->container = $container;
        $this->plugin = $plugin;
    }

    function get($key){
        return $this->container->get($key);
    }

    function addScript($name, $url, $version, $deps=array(), $footer=true){
        $this->container->get("view_assets")->addScript($name, $url, $version, $footer, $deps);
    }

    function addStyle($name, $url, $version, $deps=array()){
        $this->container->get("view_assets")->addStyle($name, $url, $version, $deps);
    }


    function assignToView($key, $value){
        $this->container->get("view")->set($key, $value);
    }


    function generateUrl($url, $params=array(), $absolute=false){
        return $this->container->get("router")->generateUrl($url, $params, $absolute);
    }


    function resultTemplate($template, $params=array()){

        foreach($params as $name=>$param){
            $this->container->get("view")->set($name, $param);
        }

        return new WPTemplateActionResult(
            $template,
	        $this->plugin->getTemplateLocations()
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

    function setTitle($title) {

        add_filter( 'wp_title', function () use($title) { return $title; }, 10, 2 );
    }

    function setDescription($description) {

        add_filter('bloginfo',function($info, $show) use($description) {
            if ($show == 'description') {
                return $description;
            }
            return $info;
        },10,2);
    }




} 