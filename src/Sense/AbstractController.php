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

abstract class AbstractController {

    private $_container;

    function __construct(Sense $sense){
        $this->_container = $sense;
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

        $response = new Response($content, $code, $headers);

        $result =  new HTTPResponseActionResult();
        $result->setResponse($response);

        return $response;

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

//    function view(){
//
//        global $template;
//
//        $template_file = \get_template_directory() . "/src/Views/" . ucfirst($this->name) . "/" . $this->view . ".php";
//
//        if(file_exists($template_file)){
//            return $template_file;
//        }
//
//
//
//        return $template;
//    }





} 