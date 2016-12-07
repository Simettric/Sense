<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 1:48
 */

namespace Simettric\Sense\ActionResult;



use Collections\Exceptions\Exception;

class WPTemplateActionResult implements  ActionResultInterface
{


    private $template_file;

	private $locations;

    public function __construct($template_file, $template_locations=array())
    {
        $this->template_file = $template_file;
	    $this->locations = $template_locations;
    }


    public function execute()
    {

        \add_filter('template_include', array($this, "templateInclude"));
    }

    /**
     * called by \add_filter( 'template_include' );
     * @return mixed
     */
    public function templateInclude($template)
    {


	    foreach ($this->locations as $dir) {
			$file = $dir . DIRECTORY_SEPARATOR . $this->template_file;

		    if(file_exists($file)){
                status_header( '200' );
			    return $file;
		    }

	    }


        wp_die("Template {$this->template_file} not found in any of these directories: " . implode(", ", $this->locations) );
    }

    public function getResponse(){
        return null;
    }

}
