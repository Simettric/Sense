<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 29/5/16
 * Time: 12:22
 */

namespace Simettric\Sense\Annotations;
use Simettric\Sense\AbstractPlugin;
use Simettric\Sense\Router\RouteInterface;

/**
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class AdminRoute
{



    /** @var string */
    public $path;

    /** @var string */
    public $name;

    /** @var string */
    public $page_title;

    /** @var string */
    public $menu_title;


    /** @var string */
    public $capability;

    /** @var string */
    public $icon_url;

    /** @var int */
    public $position=0;





    public $controller_class;

    public $controller_method;

	/**
	 * @var AbstractPlugin
	 */
	public $plugin;


    public $url;




    public function __construct( $data=[] )
    {

        if (isset($data['value'])) {
            $data['path'] = $data['value'];
            unset($data['value']);
        }
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \BadMethodCallException(sprintf('Unknown property "%s" on annotation "%s".', $key, get_class($this)));
            }
            $this->$key = $value;
        }

    }

    public function configure()
    {

        if(!$this->menu_title)
            $this->menu_title = $this->name;

        if(!$this->page_title)
            $this->page_title = $this->name;

        if(!$this->capability)
            $this->capability = 'manage_options';

    }




    public function getUrl()
    {
        return $this->url;
    }

    public function getControllerClassName()
    {
        return $this->controller_class;
    }

    public function getActionMethod()
    {
        return $this->controller_method;
    }

    public function setControllerClassName($class_name)
    {
        $this->controller_class = $class_name;
    }

    public function setActionMethod($method_name)
    {
        $this->controller_method = $method_name;
    }


	public function getPlugin()
    {
		return $this->plugin;
	}

	public function getName()
    {
        return $this->name;
    }

	public function setPlugin( AbstractPlugin $plugin ) {
		$this->plugin = $plugin;
	}
}
