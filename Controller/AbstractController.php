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

abstract class AbstractController extends AbstractBaseController {


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




} 