<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:56
 */

namespace Simettric\Sense\ActionResult;



use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HTTPResponseActionResult implements ActionResultInterface
{


    private $_response;

    public function setResponse(Response $response)
    {

        $this->_response = $response;

    }


    public function execute()
    {
        /**
         * @var $response Response
         */
        $response = $this->getResponse();

        if($response instanceof JsonResponse){

            \wp_send_json(json_decode($response->getContent(), true));

        }else{

            $response->send();

        }

        exit();
    }


    public function getResponse(){

        return $this->_response;

    }

}
