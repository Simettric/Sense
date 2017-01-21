<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 13/8/16
 * Time: 19:11
 */

namespace Simettric\Sense;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Kernel
{

	const HOOK_REGISTERED_SERVICES = 'sense.registered_services';

	/**
	 * @var array
	 */
	private static $configParams=array();

	/**
	 * @var Kernel
	 */
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

	private function __construct($config_params=array())
	{

		$this->container = new ContainerBuilder();

		foreach ($config_params as $key=>$value) {
			$this->container->setParameter("sense.".$key, $value);
		}

		$loader = new YamlFileLoader($this->container, new FileLocator([__DIR__ . "/Config"]));
		$loader->load('services.yml');

		$this->initCoreSubscribers();
	}

	/**
	 * @return Kernel
	 */
	public static function getInstance()
	{
		if(!self::$instance) {
			self::$instance = new Kernel(array_merge(self::$configParams, array(
				"plugins_order" => array()
			)));
		}
		return self::$instance;
	}

	/**
	 * @param array $config_params
	 *
	 */
	public static function configure($config_params=array())
	{
		self::$configParams = $config_params;
	}

	public function initCoreSubscribers()
	{

		\add_action( 'muplugins_loaded', array($this, 'onMuPluginsLoaded'));
		\add_action( 'plugins_loaded', array($this, 'onPluginsLoaded'));
		\add_action( 'after_setup_theme', array($this, 'onAfterSetupTheme'));

		\add_action( 'parse_query', array($this->container->get("router"), "match"));

		\add_action( 'init' , array($this, 'onInit'));

		add_action('admin_menu', array($this->container->get("admin.router"), "registerRouteRules"));

	}

	public function onMuPluginsLoaded()
	{
		$this->initialized = true;
	}

	public function onPluginsLoaded ()
	{

		if(!$this->initialized)
			$this->initialized = true;

		$this->loadPluggableFunctions();

	}



	public function onAfterSetupTheme()
	{

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

	public function onInit()
	{
		$this->container->get("router")->registerRouteRules();


		if($this->container->getParameter("debug_mode")){
			$this->container->get("router")->regenerateWPRouteCache();
		}
	}

	public function loadPluggableFunctions()
	{

		/**
		 * @var $plugin AbstractPlugin
		 */
		foreach($this->container->get("plugin_manager")->getPlugins() as $plugin){

			if(!$plugin->isTheme()) $plugin->registerPluggableFunctions();
		}
	}

	public function registerServices()
	{

		$this->container->setParameter('debug_mode', WP_DEBUG);

		/**
		 * @var $plugin AbstractPlugin
		 */
		foreach($this->container->get("plugin_manager")->getPlugins() as $plugin){

			$plugin->registerServices($this->container);
		}

		$this->container->compile();

		do_action(static::HOOK_REGISTERED_SERVICES);

	}

	public function registerRoutes()
	{

		/**
		 * @var $plugin AbstractPlugin
		 */
		foreach($this->container->get("plugin_manager")->getPlugins() as $plugin){

			$plugin->registerRoutes($this->container->get("router.route_container"));
			$plugin->registerAdminRoutes($this->container->get("admin.route_container"));
		}

	}


	/**
	 * @return ContainerBuilder
	 */
	public function getContainer()
	{
		return $this->container;
	}


}
