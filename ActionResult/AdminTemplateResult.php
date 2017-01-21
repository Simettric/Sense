<?php
/**
 *
 *
 * @author Asier Marqués <asiermarques@gmail.com>
 */

namespace Simettric\Sense\ActionResult;


class AdminTemplateResult implements ActionResultInterface
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
        foreach ($this->locations as $dir) {
            $file = $dir . DIRECTORY_SEPARATOR . $this->template_file;

            if(file_exists($file)){
                status_header( '200' );
                include $file;
                return;
            }

        }


        wp_die("Template {$this->template_file} not found in any of these directories: " . implode(", ", $this->locations) );
    }
}