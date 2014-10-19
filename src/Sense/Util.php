<?php
/**
 * Created by PhpStorm.
 * User: asierm
 * Date: 05/09/14
 * Time: 12:30
 */

namespace Sense;


class Util {

    function getArrayValue($key, array $array, $default=null){

        if(isset($array[$key])){
            return $array[$key];
        }

        return $default;

    }


} 