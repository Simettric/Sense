<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 27/06/14
 * Time: 19:17
 */

namespace Sense;


class AssetManager {

    private $_scripts=array();
    private $_stylesheets=array();

    private $_admin_mode=false;

    /**
     * @param bool $admin_mode
     */
    function __construct($admin_mode=false){
        $this->_admin_mode = $admin_mode;
    }

    function enqueueAssets(){
        if($this->_admin_mode){
            \add_action( 'admin_enqueue_scripts',  array($this, 'onEnqueueScriptsAction'));
        }else{
            \add_action( 'wp_enqueue_scripts',  array($this, 'onEnqueueScriptsAction'));
        }
    }



    function loadConfigurationParameters(array $config){

    }

    function addScript($handle, $url=null, $ver=1, $in_footer=true, $dependencies=array()){
        $this->_scripts[$handle] = array(
            'src'       => $url,
            'deps'      => $dependencies,
            'ver'       => $ver,
            'in_footer' => $in_footer
        );
    }

    function addStyle($handle, $url=null, $ver=1, $dependencies=array(), $media="all"){
        $this->_stylesheets[$handle] = array(
            'src'       => $url,
            'deps'      => $dependencies,
            'ver'       => $ver,
            'media'     => $media
        );
    }

    function onEnqueueScriptsAction(){
        foreach($this->_scripts as $handle=>$item){

            if($item["src"]){
                \wp_enqueue_script( $handle, $item["src"], $item["deps"], $item["ver"], $item["in_footer"] );
            }else{
                \wp_enqueue_script( $handle );
            }


        }
        foreach($this->_stylesheets as $handle=>$item){

            if($item["src"]){
                \wp_enqueue_style( $handle, $item["src"], $item["deps"], $item["ver"], $item["media"] );
            }else{
                \wp_enqueue_style( $handle );
            }
        }
    }
} 