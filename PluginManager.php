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
     * @var Collection
     */
    private $plugins;







    function __construct(){

        $this->plugins    = new Collection("Simettric\\Sense\\AbstractPlugin");
    }





    function register(AbstractPlugin $plugin, $file){

        $this->plugins->add($plugin);

        if($plugin->isTheme()){

            add_action( 'after_switch_theme', array($plugin, "onActivate"));

        }else{

            register_activation_hook( $file, array($plugin, "onActivate"));

        }

    }



    function getPlugins(){
        return $this->plugins;
    }






} 