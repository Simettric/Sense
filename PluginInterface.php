<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 21:36
 */

namespace Simettric\Sense;


interface PluginInterface {

    function getConfigLocations();

    function getTemplateLocations();

    function getName();

} 