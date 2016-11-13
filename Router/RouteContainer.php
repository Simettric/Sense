<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/6/16
 * Time: 14:13
 */

namespace Simettric\Sense\Router;


use Collections\Collection;

class RouteContainer extends Collection
{

    public function __construct(){
        parent::__construct("Simettric\\Sense\\Router\\RouteInterface");
    }

    public function get($name){
        return $this->find(function(RouteInterface $route) use ($name){
                return $route->getName() == $name;
        });
    }

}
