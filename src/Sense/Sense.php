<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 27/06/14
 * Time: 2:30
 */

namespace Sense;


use Pimple\Container;
use Sense\Config\Loader;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;

class Sense extends Container {

    private $_path_dir;
    private $_config_dirs=array();
    private $_cache_dir;

    function __construct($path_dir, array $config_dirs, $cache_dir=null){

        $this->_path_dir    = $path_dir;
        $this->_cache_dir   = $cache_dir ? $cache_dir : WP_CONTENT_DIR . "/cache/";


        \array_unshift( $config_dirs, __DIR__ . "/../../config" );

        $this->_config_dirs = $config_dirs;

        parent::__construct();
    }


    function init(){
        $this->_setCoreValues();
        $this->_setConfigValues();
        \add_action('wp', array($this, '_onWPAction'));

    }

    function setConfigDirectories($directory){

    }

    function _onWPAction(\WP $wp){

        $this["sense.theme_assets"]->enqueueAssets();
        $this["sense.admin_assets"]->enqueueAssets();

    }

    private function _setConfigValues(){

        $cache_path       = $this->_cache_dir . "config.php";
        $userMatcherCache = new ConfigCache($cache_path, $this["%wp.debug_mode%"]);
//
        if (true || !$userMatcherCache->isFresh()) {

            $locator = new FileLocator($this->_config_dirs);
            $files   = $locator->locate('config.yml', null, false);
            //$loaderResolver   = new LoaderResolver(array(new Loader($locator)));
            //$delegatingLoader = new DelegatingLoader($loaderResolver);
            $delegatingLoader = new Loader($locator);

            $resources = array();

            foreach ($files as $yamlUserFile) {

                $delegatingLoader->load($yamlUserFile);
                $resources[] = new FileResource($yamlUserFile);
            }

            $config = $delegatingLoader->process();


//
//            // the code for the UserMatcher is generated elsewhere
            $code = var_export($config, true);
//
            $userMatcherCache->write(serialize($code), $resources);

        }else{
            $config  = require $cache_path;
            $config  = unserialize($config);
        }



        $theme_assets = $config["assets"]["theme"];

        foreach($theme_assets["scripts"] as $handle=>$params){
            /**
             * @var $this["sense.theme_assets"] AssetManager
             */
            $this["sense.theme_assets"]->addScript(
                $handle,
                $this["%wp.template_uri%"] . $params["file"],
                1,
                true,
                $dependencies=$params["dependencies"]
            );
        }

        foreach($theme_assets["styles"] as $handle=>$params){
            /**
             * @var $this["sense.theme_assets"] AssetManager
             */
            $this["sense.theme_assets"]->addStyle(
                $handle,
                $this["%wp.template_uri%"] . $params["file"]
            );
        }

    }



    private function _setCoreValues(){
        $this["%wp.debug_mode%"]    = WP_DEBUG;
        $this["%wp.template_uri%"]  = \get_template_directory_uri();
        $this["%wp.plugin_uri%"]    = \plugin_dir_url($this->_path_dir);
        $this["sense.theme_assets"] = new AssetManager();
        $this["sense.admin_assets"] = new AssetManager(true);
    }





}