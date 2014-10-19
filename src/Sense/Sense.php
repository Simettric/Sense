<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 27/06/14
 * Time: 2:30
 */

namespace Sense;


use Pimple\Container;
use Sense\Config\AssetsConfiguration;
use Sense\Config\Loader;
use Sense\Form\TemplateParser;
use Sense\Model\UserModel;
use SimpleForm\Config;
use SimpleForm\FormBuilder;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Component\Form\Extension\Templating\TemplatingExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Validator\Validation;

class Sense extends Container {

    private $_path_dir;
    private $_config_dirs=array();
    private $_cache_dir;

    function __construct(AbstractTheme $theme, $path_dir){


        $this->startSessions();

        $this->_path_dir    = $path_dir;
        $this->_cache_dir   = WP_CONTENT_DIR . "/cache/";



        $this["%sense.app_dir%"]  = $theme->getDir();
        $this["%sense.app.mode%"] = "template";
        $config_dirs = (array) ($this["%sense.app_dir%"] . "/Config");




        \array_unshift( $config_dirs, __DIR__ . "/../../config" );
        $this->_config_dirs = $config_dirs;




        $theme->setUp();
        $this["theme"] = function($c) use($theme){
            return $theme;
        };

        parent::__construct();

        $this->_setCoreValues();
        $this->_setConfigValues();
    }


    function init(){



        $this["theme"]->assign("sense.model.user", $this["sense.model.user"]);
        $this["theme"]->assign("request", $this["request"]);
        $this["theme"]->assign("wp.query", $this["wp.query"]);

        $this["theme"]->setRouter($this["router"]);
        $this["theme"]->setUserModel($this["sense.model.user"]);
        $this["router"]->init();


        \is_admin() ? $this["sense.admin_assets"]->enqueueAssets() : $this["sense.theme_assets"]->enqueueAssets();

    }

    function startSessions(){
        if(!session_id()){
            $upload_dir = wp_upload_dir();

            if(!is_dir($upload_dir['basedir'] . "/sessions/")){
                mkdir($upload_dir['basedir'] . "/sessions/", 0777);
            }

            ini_set("session.save_path", $upload_dir['basedir'] . "/sessions/");
            session_start(); //required for flash messages
        }
    }

    function setConfigDirectories($directory){

    }



    private function _setConfigValues(){

        $cache_path          = $this->_cache_dir . "config.php";
        $userMatcherCache    = new ConfigCache($cache_path, $this["%wp.debug_mode%"]);
        $assetsConfiguration = new AssetsConfiguration();

        if (!$userMatcherCache->isFresh()) {

            $locator = new FileLocator($this->_config_dirs);
            $files   = $locator->locate('config.yml', null, false);

            $delegatingLoader = new Loader($locator);
            $config = $delegatingLoader->process($files, $assetsConfiguration, $userMatcherCache);

        }else{
            $config  = unserialize(require $cache_path);
        }


        $assetsConfiguration->setAssets($config, $this, \is_admin() );

    }






    private function _setCoreValues(){

        $this["%wp.debug_mode%"]    = WP_DEBUG;
        $this["%wp.template_uri%"]  = \get_template_directory_uri();
        $this["%wp.plugin_uri%"]    = \plugin_dir_url($this->_path_dir);

        $this["%sense.app.vendors_dir%"] = $this["%sense.app.mode%"] == "template" ? get_template_directory() . "/vendors/" :  plugin_dir_path() . "/vendors/" ;

        $this["sense.theme_assets"] = function($c){
            return new AssetManager();
        };
        $this["sense.admin_assets"] = function($c){
            return new AssetManager(true);
        };
        $this["sense.model.user"] = function($c){
            return new UserModel();
        };

        $this["router"] = function($c){
            return new Router($c);
        };

        $this["wp.query"] = function($c){
            global $wp_query;
            return $wp_query;
        };

        $this["request"]  = function($c){
           return Request::createFromGlobals();
        };

        $this["util"] = function(){
            return new Util();
        };


        $this["sense.form.config"] = function($c){
            return new Config();
        };
        $this["sense.form.builder"] = function($c){
            return new FormBuilder($c["sense.form.config"]);
        };



    }





}