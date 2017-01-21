<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 1:47
 */

namespace Simettric\Sense\Tests\Router;


use Simettric\Sense\Controller\AbstractAdminController;
use Simettric\Sense\Controller\AbstractController;
use Simettric\Sense\Annotations\Route;
use Simettric\Sense\Annotations\AdminRoute;

class DummyDefaultAdminController extends AbstractAdminController
{

    /**
     *  @AdminRoute("/demo/",
     *     name="name"
     *
     *   )
     */
    public function fakeAdminAction(){
        return $this->resultResponse("test");
    }

}
