<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 1:07
 */

namespace Simettric\Sense\Tests\Router;


use Simettric\Sense\Router\AbsoluteUrlGeneratorInterface;

class DummyUrlAbsoluteGenerator implements AbsoluteUrlGeneratorInterface{

    function createUrl($path)
    {
        return "http://example.com/es" . $path;
    }

}
 