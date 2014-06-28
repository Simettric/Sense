<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 27/06/14
 * Time: 22:30
 */

namespace Sense\Config;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

class Loader extends FileLoader{


    private $_values=array();


    public function load($resource, $type = null)
    {

        $this->_values[] = Yaml::parse($resource);

    }


    function process(array $files, ConfigurationInterface $configuration, ConfigCache $cache=null){

        $resources = array();
        foreach ($files as $yamlUserFile) {

            $this->load($yamlUserFile);
            $resources[] = new FileResource($yamlUserFile);
        }
        $processor     = new Processor();
        $values        = $processor->processConfiguration($configuration, $this->_values);


        if($cache){
            $code = "<?php return '" . serialize($values) . "';";
            $cache->write($code, $resources);
        }

        return $values;

    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}
