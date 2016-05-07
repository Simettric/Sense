<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 30/07/14
 * Time: 21:04
 */

namespace Sense\ActionResult;



class WPTemplateActionResult extends AbstractActionResult  {


    /**
     * called by \add_filter( 'template_include' );
     * @return mixed
     */
    function templateInclude(){
        global $template;



        $template_file = $this->_params["template"];


        if(file_exists($template_file)){
            return $template_file;
        }

        return $template;
    }

    function getResponse(){
        return null;
    }

} 