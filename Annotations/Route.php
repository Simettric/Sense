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
class Route implements RouteInterface
{

    /** @var string */
    public $path;

    /** @var string */
    public $regexp;

    /** @var array */
    public $url_params=array();

    /** @var array */
    public $params=array();

    /** @var array */
    public $requirements=array();

    /** @var string */
    public $url;

    /** @var string */
    public $name;

    /** @var string */
    public $method="GET";

    public $controller_class;

    public $controller_method;

	/**
	 * @var AbstractPlugin
	 */
	public $plugin;




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

	    $this->params["__route_name"] = $this->name;

	    if(0===strpos($this->path, "/")){
		    $this->path = substr($this->path, 1, strlen($this->path));
	    }

	    if((strlen($this->path)-1)===strrpos($this->path, "/")){
		    $this->path = substr($this->path, 0, strlen($this->path)-1);
	    }

        \preg_match_all('({\w+})', $this->path, $found_params);
        $found_params = isset($found_params[0]) && is_array($found_params[0]) ? $found_params[0] : array();

        $this->url_params = array();
        $regexp = $this->path;
        foreach($found_params as $i=>$_param){
            $_key   = str_replace(array("{","}"), "", $_param);
            $_expr = !isset($this->requirements[$_key]) ? '(\w+)' : $this->requirements[$_key];
            $regexp   = str_replace($_param, $_expr, $regexp);
            $this->url_params[$_key] = '$matches['.($i+1).']';
        }

        foreach ($this->requirements as $key=>$expr)
        {
            $this->requirements[$key] = "/$expr/";
        }

        $this->regexp =  $regexp . "/?$" ;

        $params = array_merge($this->params, $this->url_params);

        $url = "index.php?" . http_build_query($params, '', "&");
        $this->url = urldecode($url);


    }


    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getUrlParams()
    {
        return $this->url_params;
    }

    public function getRegExp()
    {
        return $this->regexp;
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

    public function getHTTPMethod()
    {
        return $this->method;
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

	public function setPlugin( AbstractPlugin $plugin ) {
		$this->plugin = $plugin;
	}
}
