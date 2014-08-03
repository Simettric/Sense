<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 30/07/14
 * Time: 21:01
 */

namespace Sense\ActionResult;


abstract class AbstractActionResult {

    protected $_params=array();

    function __construct(array $params=array()){
        $this->_params = $params;
    }


    /**
     * @return null|mixed
     */
    abstract function  getResponse();


} 