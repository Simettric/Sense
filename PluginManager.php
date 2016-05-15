<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:39
 */

namespace Simettric\Sense;


use Collections\Collection;
use Simettric\Sense\Router\RouteContainer;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PluginManager {


    /**
     * @var bool
     */
    private $debug_mode;


    /**
     * @var Collection
     */
    private $plugins;

    /**
     * @var ContainerInterface
     */
    private $container;





    private static $instance;


    private function __construct(){
        $this->debug_mode = WP_DEBUG;
        $this->plugins    = new Collection("Simettric\\Sense\\AbstractPlugin");
        $this->container  = new ContainerBuilder();
        $this->container->set("router.route_container", new RouteContainer());
    }

    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new PluginManager();
        }

        return self::$instance;
    }


    function register(AbstractPlugin $plugin, $file=null){

        $plugin->loadRoutes($this->container->get("router.route_container"));

        $this->plugins->add($plugin);

        if($plugin->isTheme()){

            add_action( 'after_switch_theme', array($plugin, "onActivate"));

        }else{

            register_activation_hook( $file, array($plugin, "onActivate"));

        }

    }



    function registerHooks(){

        add_action("plugins_loaded", array($this, "registerServices"));
    }

    function registerServices(){

        $dirs = [];

        /**
         * @var $plugin AbstractPlugin
         */
        foreach($this->plugins as $plugin){

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