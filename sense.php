<?php
/*
Plugin Name: The Sense WordPress Framework
Plugin URI:  http://simettric.com/sense
Description: Sense is a MVC Framework designed to build complex websites and web applications based in WordPress.
Version:     2.0.0
Author:      Asier Marqués <asiermarques@gmail.com>
Author URI:  http://simettric.com
License:     MIT
License URI: https://opensource.org/licenses/MIT
Text Domain: sim_sense
*/

require __DIR__ . "/vendor/autoload.php";

$plugin = new \Simettric\Sense\Plugin();
$plugin->init(WP_DEBUG);



