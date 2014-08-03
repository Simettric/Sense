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
use Sense\Model\UserModel;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\HttpFoundation\Request;

class Sense extends Container {

    private $_path_dir;
    private $_config_dirs=array();
    private $_cache_dir;

    function __construct(AbstractTheme $theme, $path_dir){

        $this->_path_dir    = $path_dir;
        $this->_cache_dir   = WP_CONTENT_DIR . "/cache/";



        $this["%sense.app_dir%"]  = $theme->getDir();
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

        $this["theme"]->setRouter($this["router"]);

        $this["theme"]->assign("sense.model.user", $this["sense.model.user"]);
        $this["theme"]->assign("request", $this["request"]);

        $this["router"]->init();


        \is_admin() ? $this["sense.admin_assets"]->enqueueAssets() : $this["sense.theme_assets"]->enqueueAssets();

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



    private function  configureFormService(){
        // Overwrite this with your own secret
        define('CSRF_SECRET', 'c2ioeEU1n48QF2WsHGWd2HmiuUUT6dxr');

        define('VENDOR_DIR', realpath(__DIR__ . '/../vendor'));
        define('VENDOR_FORM_DIR', VENDOR_DIR . '/symfony/form/Symfony/Component/Form');
        define('VENDOR_VALIDATOR_DIR', VENDOR_DIR . '/symfony/validator/Symfony/Component/Validator');
        define('VENDOR_FRAMEWORK_BUNDLE_DIR', VENDOR_DIR . '/symfony/framework-bundle/Symfony/Bundle/FrameworkBundle');
        define('VIEWS_DIR', realpath(__DIR__ . '/../views'));

// Set up the CSRF provider
        $csrfProvider = new DefaultCsrfProvider(CSRF_SECRET);

// Set up the Validator component
        $validator = Validation::createValidator();

//// Set up the Translation component
//        $translator = new Translator('en');
//        $translator->addLoader('xlf', new XliffFileLoader());
//        $translator->addResource('xlf', VENDOR_FORM_DIR . '/Resources/translations/validators.en.xlf', 'en', 'validators');
//        $translator->addResource('xlf', VENDOR_VALIDATOR_DIR . '/Resources/translations/validators.en.xlf', 'en', 'validators');

// Set up the Templating component
        $engine = new PhpEngine(new SimpleTemplateNameParser(VIEWS_DIR), new FilesystemLoader(array()));
//        $engine->addHelpers(array(new TranslatorHelper($translator)));

// Set up the Form component
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new CsrfExtension($csrfProvider))
            ->addExtension(new TemplatingExtension($engine, null, array(
                // Will hopefully not be necessary anymore in 2.2
                VENDOR_FRAMEWORK_BUNDLE_DIR . '/Resources/views/Form',
            )))
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();


        $this["sense.formFactory"] = function($c) use($formFactory){
            return $formFactory;
        };
    }


    private function _setCoreValues(){

        $this["%wp.debug_mode%"]    = WP_DEBUG;
        $this["%wp.template_uri%"]  = \get_template_directory_uri();
        $this["%wp.plugin_uri%"]    = \plugin_dir_url($this->_path_dir);
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



    }





}