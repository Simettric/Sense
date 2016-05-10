<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 23:01
 */

namespace Simettric\Sense\Controller;


use Collections\Collection;

class InterceptorCollection extends Collection{

    function __construct(){
        parent::__construct("\\Simettric\\Sense\\Controller\\AbstractInterceptor");
    }

} 