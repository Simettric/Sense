<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 29/5/16
 * Time: 12:22
 */

namespace Simettric\Sense\Annotations;
use Simettric\Sense\Router\RouteInterface;

/**
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class Route implements RouteInterface{

    /** @var string */
    public $path;

    /** @var string */
    public $regexp;

    /** @var array */
    public $url_params=array();

    /** @var array */
    public $params=array();

    /** @var string */
    public $url;

    /** @var string */
    public $name;

    public $controller_class;

    public $controller_method;




    function __construct( $data=[] ){

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

    function configure(){


        \preg_match_all('({\w+})', $this->path, $found_params);
        $found_params = isset($found_params[0]) && is_array($found_params[0]) ? $found_params[0] : array();

        $this->url_params = array();
        $regexp = $this->path;
        foreach($found_params as $i=>$_param){
            $_key   = str_replace(array("{","}"), "", $_param);
            $_expr = !isset($requirements[$_key]) ? '(\w+)' : $requirements[$_key];
            $regexp   = str_replace($_param, $_expr, $regexp);
            $this->url_params[$_key] = '$matches['.($i+1).']';
        }

        $this->regexp = '^' . $regexp . "$" ;

        $params = array_merge($this->params, $this->url_params);

        $url = "index.php?" . http_build_query($params, '', "&");
        $this->url = urldecode($url);

    }


    function getName(){
        return $this->name;
    }

    function getPath(){
        return $this->path;
    }

    function getParams(){
        return $this->params;
    }

    function getUrlParams(){
        return $this->url_params;
    }

    function getRegExp(){
        return $this->regexp;
    }

    function getUrl(){
        return $this->url;
    }

    function getControllerClassName()
    {
        return $this->controller_class;
    }

    function getActionMethod()
    {
        return $this->controller_method;
    }

    function setControllerClassName($class_name)
    {
        $this->controller_class = $class_name;
    }

    function setActionMethod($method_name)
    {
        $this->controller_method = $method_name;
    }





}