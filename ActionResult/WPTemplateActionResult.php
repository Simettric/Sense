<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:48
 */

namespace Simettric\Sense\ActionResult;


class WPTemplateActionResult implements  ActionResultInterface  {


    private $template_file;

    function __construct($template_file){
        $this->template_file = $template_file;
    }


    function execute(){
        \add_filter('template_include', array($this, "templateInclude"));
    }

    /**
     * called by \add_filter( 'template_include' );
     * @return mixed
     */
    function templateInclude(){
        global $template;

        if(file_exists($this->template_file)){
            return $this->template_file;
        }

        return $template;
    }

    function getResponse(){
        return null;
    }

} 