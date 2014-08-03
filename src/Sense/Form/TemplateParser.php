<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 3/08/14
 * Time: 21:11
 */

namespace Sense\Form;

use Symfony\Component\Templating\TemplateReference;
use Symfony\Component\Templating\TemplateNameParserInterface;

class TemplateParser implements TemplateNameParserInterface
{
    private $root;

    public function __construct($root)
    {
        $this->root = $root;
    }

    public function parse($name)
    {
        if (false !== strpos($name, ':')) {
            $path = str_replace(':', '/', $name);
        } else {
            $path = $this->root . '/' . $name;
        }

        return new TemplateReference($path, 'php');
    }
}