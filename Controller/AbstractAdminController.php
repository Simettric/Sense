<?php
/**
 *
 *
 * @author Asier Marqués <asiermarques@gmail.com>
 */

namespace Simettric\Sense\Controller;


use Simettric\Sense\ActionResult\AdminTemplateResult;

abstract class AbstractAdminController extends AbstractController
{

    function resultTemplate($template, $params = array())
    {
        foreach($params as $name=>$param){
            $this->container->get("view")->set($name, $param);
        }

        return new AdminTemplateResult(
            $template,
            $this->plugin->getAdminTemplateLocations()
        );
    }



}