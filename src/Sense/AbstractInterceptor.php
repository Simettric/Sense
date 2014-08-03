<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 26/07/14
 * Time: 17:41
 */

namespace Sense;


use Sense\Interfaces\Interceptor;

abstract class AbstractInterceptor implements Interceptor {

    private $_sense;

    function __construct(Sense $sense){
        $this->_sense = $sense;
    }


    function get($key){
        return $this->_sense[$key];
    }

    function assignToView($key, $value){

    }



}