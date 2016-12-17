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
use Simettric\Sense\Exception\NotFoundException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var AbstractPlugin
     */
    protected $plugin;


    public function __construct(Container $container, AbstractPlugin $plugin=null)
    {
        $this->container = $container;
        $this->plugin = $plugin;
    }

    public function get($key)
    {
        return $this->container->get($key);
    }

    public function addScript($name, $url, $version, $deps=array(), $footer=true)
    {
        $this->container->get("view_assets")->addScript($name, $url, $version, $footer, $deps);
    }

    public function addStyle($name, $url, $version, $deps=array())
    {
        $this->container->get("view_assets")->addStyle($name, $url, $version, $deps);
    }


    public function assignToView($key, $value)
    {
        $this->container->get("view")->set($key, $value);
    }


    public function generateUrl($url, $params=array(), $absolute=false)
    {
        return $this->container->get("url_generator")->generateUrl($url, $params, $absolute);
    }


    public function resultTemplate($template, $params=array())
    {

        foreach($params as $name=>$param){
            $this->container->get("view")->set($name, $param);
        }

        return new WPTemplateActionResult(
            $template,
	        $this->plugin->getTemplateLocations()
        );
    }

    public function resultResponse($content, $code=200, $headers=array())
    {


        if($content instanceof Response){
            $response = $content;
        }else{
            $response = new Response($content, $code, $headers);
        }


        $result =  new HTTPResponseActionResult();
        $result->setResponse($response);

        return $result;

    }

    public function resultRedirect($url, $code=302, $headers=array())
    {

        $response = new RedirectResponse($url, $code, $headers);

        $result =  new HTTPResponseActionResult();
        $result->setResponse($response);

        return $result;
    }

    public function setTitle($title)
    {

        add_filter( 'wp_title', function () use($title) { return $title; }, 10, 2 );
    }

    public function setDescription($description)
    {

        add_filter('bloginfo',function($info, $show) use($description) {
            if ($show == 'description') {
                return $description;
            }
            return $info;
        },10,2);
    }

    protected function createNotFoundException()
    {
        return new NotFoundException();
    }

}
