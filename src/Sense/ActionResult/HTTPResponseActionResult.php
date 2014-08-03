<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 31/07/14
 * Time: 14:13
 */

namespace Sense\ActionResult;


use Symfony\Component\HttpFoundation\Response;

class HTTPResponseActionResult extends AbstractActionResult {


    private $_response;

    function setResponse(Response $response){

        $this->_response = $response;

    }


    function getResponse(){

        return $this->_response;

    }

} 