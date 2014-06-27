<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 27/06/14
 * Time: 2:30
 */

namespace Sense;


use Pimple\Container;

class Sense extends Container {


    function __construct(){
        parent::__construct();
    }


    function init(){
        $this->_setCoreValues();
        $this->_setConfigValues();
        \add_action('wp', array($this, '_onWPAction'));

    }

    function _onWPAction(\WP $wp){

        $this["sense.theme_assets"]->enqueueAssets();
        $this["sense.admin_assets"]->enqueueAssets();

    }

    private function _setConfigValues(){

        foreach(array(
                    'jquery'    => array('file'=>false, 'dependencies'=>array()),
                    'bootstrap' => array('file'=>'/js/bootstrap.min.js', 'dependencies'=>array('jquery'))
                ) as $handle=>$config){
            /**
             * @var $this["sense.theme_assets"] AssetManager
             */
            $this["sense.theme_assets"]->addScript(
                $handle,
                $this["%wp.template_uri%"] . $config["file"],
                1,
                true,
                $dependencies=$config["dependencies"]
            );
        }

        foreach(array(
                    'bootstrap' => array('file'=>'/css/bootstrap.min.css')
                ) as $handle=>$config){
            /**
             * @var $this["sense.theme_assets"] AssetManager
             */
            $this["sense.theme_assets"]->addStyle(
                $handle,
                $this["%wp.template_uri%"] . $config["file"]
            );
        }

    }



    private function _setCoreValues(){
        $this["%wp.debug_mode%"]    = WP_DEBUG;
        $this["%wp.template_uri%"]  = \get_template_directory_uri();
        $this["sense.theme_assets"] = new AssetManager();
        $this["sense.admin_assets"] = new AssetManager(true);
    }





}