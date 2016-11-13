<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:39
 */

namespace Simettric\Sense;


use Collections\Collection;

class PluginManager
{


    /**
     * @var Collection
     */
    private $plugins;


    public function __construct()
    {

        $this->plugins    = new Collection("Simettric\\Sense\\AbstractPlugin");
    }


    public function register(AbstractPlugin $plugin, $file)
    {

        $this->plugins->add($plugin);

        if($plugin->isTheme()){

            add_action( 'after_switch_theme', array($plugin, "onActivate"));

        }else{

            register_activation_hook( $file, array($plugin, "onActivate"));

        }

    }

    public function getPlugins()
    {
        return $this->plugins;
    }

}
