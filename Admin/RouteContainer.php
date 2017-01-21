<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/6/16
 * Time: 14:13
 */

namespace Simettric\Sense\Admin;


use Collections\Collection;
use Simettric\Sense\Annotations\AdminRoute;

class RouteContainer extends Collection
{

    public function __construct(){
        parent::__construct("Simettric\\Sense\\Annotations\\AdminRoute");
    }

    public function get($name){
        return $this->find(function(AdminRoute $route) use ($name){
                return $route->getName() == $name;
        });
    }

}
