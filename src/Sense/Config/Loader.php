<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 27/06/14
 * Time: 22:30
 */

namespace Sense\Config;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class Loader extends FileLoader{


    private $_values=array();


    public function load($resource, $type = null)
    {

        $this->_values[] = Yaml::parse($resource);

    }


    function process(){


        $processor     = new Processor();
        $configuration = new AssetsConfiguration();
        return $processor->processConfiguration(
            $configuration,
            $this->_values
        );
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}
