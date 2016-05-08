<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 15:28
 */

namespace Simettric\Sense\Router;


class DefaultWPUrlAbsoluteGenerator implements AbsoluteUrlGeneratorInterface{


	function createUrl( $path ) {
		return \home_url($path);
	}
}