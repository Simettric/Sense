<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 1:47
 */

namespace Simettric\Sense\Tests\Router;


use Simettric\Sense\Controller\AbstractController;

class DummyController extends AbstractController{

    function fakeAction(){
        return $this->resultResponse("test");
    }

} 