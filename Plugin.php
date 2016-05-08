<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:39
 */

namespace Simettric\Sense;


use Collections\Collection;

class Plugin {


    /**
     * @var bool
     */
    private $debug_mode;


    /**
     * @var Collection
     */
    private $plugins;


    function __construct(){
        $this->plugins = new Collection("Simettric\\Sense\\PluginInterface");
    }


    function init($debug_mode=false){
        $this->debug_mode = $debug_mode;
    }


    function register(PluginInterface $plugin){
        $this->plugins->add($plugin);
    }







} 