<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 22:28
 */

namespace Simettric\Sense\Subscriber;


use Simettric\Sense\Controller\InterceptorCollection;
use Simettric\Sense\Controller\AbstractInterceptor;
use Simettric\Sense\Router\Router;

class KernelHookSubscriber implements WPHookSubscriberInterface
{

    /**
     * @var Router
     */
    private $router;

    /**
     * @var InterceptorCollection
     */
    private $interceptorCollection;


    /**
     * @var \WP_Query
     */
    private $wp_query;


    public function __construct(\WP_Query $query,
                         Router $router,
                         InterceptorCollection $interceptorCollection)
    {

        $this->router = $router;
        $this->interceptorCollection = $interceptorCollection;
        $this->wp_query = $query;

    }


    public function registerHooks()
    {
        \add_action("parse_query", array($this->router, "match"));
        \add_action('pre_get_posts', array($this, "executeInterceptors"));
    }

    public function executeInterceptors(){

        /**
         * @var $interceptor AbstractInterceptor
         */
        foreach($this->interceptorCollection as $interceptor){

            if($interceptor->canBeExecuted()){
                $interceptor->execute($this->wp_query);
            }

        }

    }


}
