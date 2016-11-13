<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 13/11/16
 * Time: 14:43
 */

function sense_view()
{
    return Simettric\Sense\Kernel::getInstance()->getContainer()->get("view");
}

function sense_url($route_name, $params=[], $absolute=false)
{

    $generator = Simettric\Sense\Kernel::getInstance()->getContainer()->get("url_generator");
    return $generator->generateUrl($route_name, $params, $absolute);
}