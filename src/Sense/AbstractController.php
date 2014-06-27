<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 27/06/14
 * Time: 19:14
 */

namespace Sense;


abstract class AbstractController {

    private $_container;

    function __construct(Sense $sense){
        $this->_container = $sense;
    }





} 