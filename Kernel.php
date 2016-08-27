<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 13/8/16
 * Time: 19:11
 */

namespace Simettric\Sense;


use Simettric\Sense\Router\DefaultWPUrlAbsoluteGenerator;
use Simettric\Sense\Router\RouteContainer;
use Simettric\Sense\Router\Router;
use Simettric\Sense\View\View;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;

class Kernel {

	private static $instance=null;

	/**
	 * This controls if Sense Kernel has been initialized
	 * It´s useful when you are using a theme witch uses Sense but
	 * there is not any plugin that initializes Sense
	 * @var bool
	 */
	private $initialized=false;

	/**
	 * @var ContainerBuilder
	 */
	private $container;

	private function __construct($config_params=array()) {

		$this->container = new ContainerBuilder();

		foreach ($config_params as $key=>$value) {
			$this->container->setParameter("sense.".$key, $value);
		}

		$loader = new YamlFileLoader($this->container, new FileLocator([__DIR__ . "/Config"]));
		$loader->load('services.yml');

		$this->initCoreSubscribers();
	}

	static function getInstance() {
		if(!self::$instance) {
			throw new Exception("You need to initialize the Sense Kernel in wp_config.php ");
		}
		return self::$instance;
	}

	static function init($config_params=array()){
		if(self::$instance) {
			throw new Exception("You only can initialize the Sense Kernel once. It must be in wp_config.php");
		}
		self::$instance = new Kernel(array_merge($config_params, array(
			"plugins_order" => array()
		)));
	}

	function initCoreSubscribers() {

		add_action( 'muplugins_loaded', array($this, 'onMuPluginsLoaded'));
		add_action( 'plugins_loaded', array($this, 'onPluginsLoaded'));
		add_action( 'after_setup_theme', array($this, 'onAfterSetupTheme'));

		add_action( 'parse_query', array($this->container->get("router"), "match"));
		add_action( 'init' , array($this, 'onInit'));

	}

	function onMuPluginsLoaded() {
		$this->initialized = true;
	}

	function onPluginsLoaded (){

		if(!$this->initialized)
			$this->initialized = true;

		$this->loadPluggableFunctions();

	}



	function onAfterSetupTheme(){

		if(!$this->initialized){

			$notice = "You need to activate almost one plugin using Sense in order to use it in a Theme";
			if(!is_admin() && $GLOBALS['pagenow'] != 'wp-login.php') {

				wp_die($notice, "Sense Error");

			}else{
				add_action( 'admin_notices', function () use ($notice) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><strong>Sense Framework</strong>: <?php echo $notice; ?></p>
					</div>
					<?php
				});
			}
		}

		$this->registerServices();

		$this->registerRoutes();
	}

	function onInit(){
		$this->container->get("router")->registerRouteRules();


		if($this->container->getParameter("debug_mode")){
			$this->container->get("router")->regenerateWPRouteCache();
		}
	}

	function loadPluggableFunctions(){

		/**
		 * @var $plugin AbstractPlugin
		 */
		foreach($this->container->get("plugin_manager")->getPlugins() as $plugin){

			if(!$plugin->isTheme()) $plugin->registerPluggableFunctions();
		}
	}

	function registerServices(){



		$this->container->setParameter('debug_mode', WP_DEBUG);

		global $wp_query;
		$this->container->set('wp.query', $wp_query);


		/**
		 * @var $plugin AbstractPlugin
		 */
		foreach($this->container->get("plugin_manager")->getPlugins() as $plugin){

			$plugin->registerServices($this->container);
		}

		$this->container->compile();

	}

	function registerRoutes(){

		/**
		 * @var $plugin AbstractPlugin
		 */
		foreach($this->container->get("plugin_manager")->getPlugins() as $plugin){

			$plugin->registerRoutes($this->container->get("router.route_container"));
		}




	}
	/**
	 * @return ContainerBuilder
	 */
	function getContainer(){
		return $this->container;
	}



}

