<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 13/8/16
 * Time: 19:11
 */

namespace Simettric\Sense;


use Simettric\Sense\Router\RouteContainer;
use Simettric\Sense\Router\Router;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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

	private function __construct() {

		$this->container = new ContainerBuilder();

		$this->container->setParameter('debug_mode', WP_DEBUG);

		$this->container
			 ->register('plugin_manager', PluginManager::class)
		     ->addArgument($this->container);

		$this->container->register('router.route_container', RouteContainer::class);

		$this->container
			 ->register('router', Router::class)
			 ->addArgument($this->container);

		$this->initSubscribers();
	}

	static function getInstance() {
		if(!self::$instance) {
			self::$instance = new Kernel();
		}
		return self::$instance;
	}

	function initSubscribers() {

		add_action( 'muplugins_loaded', array($this, 'onMuPluginsLoaded'));
		add_action( 'plugins_loaded', array($this, 'onPluginsLoaded'));
		add_action( 'after_setup_theme', array($this, 'onAfterSetupTheme'));
		add_action( 'parse_query', array($this->container->get("router"), "match"));

	}

	function onMuPluginsLoaded() {
		$this->initialized = true;
	}

	function onPluginsLoaded (){

		$this->registerServices();

		$this->registerRoutes();

		$this->loadPluggableFunctions();

		$this->container->get("router")->init();

	}

	function onAfterSetupTheme(){
		if(!$this->initialized){

			$notice = "You need to activate almost one plugin using Sense in order to use it in a Theme";
			if(!is_admin()) {
				wp_die($notice, "Sense Error");

			}else{
				add_action( 'admin_notices', function () use ($notice) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php echo $notice; ?></p>
					</div>
					<?php
				});
			}
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

		/**
		 * @var $plugin AbstractPlugin
		 */
		foreach($this->container->get("plugin_manager")->getPlugins() as $plugin){

			$plugin->registerServices($this->container);
		}

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

