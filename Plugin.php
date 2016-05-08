<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:39
 */

namespace Simettric\Sense;


class Plugin {


    /**
     * @var bool
     */
    private $debug_mode;

    function init($debug_mode=false){
        $this->debug_mode = $debug_mode;
    }



} 