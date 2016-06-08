<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/6/16
 * Time: 13:40
 */

namespace Simettric\Sense;


use Collections\Collection;
use Simettric\Sense\Router\RouteContainer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Kernel {


    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var PluginManager
     */
    private $plugin_manager;

    /**
     * @var $this
     */
    private static $instance;

    private function __construct(){


        $this->container  = new ContainerBuilder();
        $this->container->set("router.route_container", new RouteContainer());
        $this->container->setParameter("app.debug", WP_DEBUG);

        $this->plugin_manager = new PluginManager($this->container);


        $this->registerHooks();

    }

    static function getInstance(){
        if(!self::$instance){
            self::$instance = new Kernel();
        }
        return self::$instance;
    }


    function getPluginManager(){
        return $this->plugin_manager;
    }


    function registerHooks(){

        add_action("plugins_loaded", array($this, "registerServices"));

    }



    function registerServices(){

        $dirs = [];

        /**
         * @var $plugin AbstractPlugin
         */
        foreach($this->plugin_manager->getPlugins() as $plugin){

            if(!is_array($plugin->getConfigLocations()))
                throw new \Exception("getConfigLocations must to return an array in " . get_class($plugin));

            foreach($plugin->getConfigLocations() as $dir){
                $dirs[] = $dir;
            }
        }

        if(count($dirs)){
            $loader = new YamlFileLoader($this->container, new FileLocator($dirs));
            $loader->load('services.yml');
        }

    }

} 