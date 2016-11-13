<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 8/5/16
 * Time: 15:00
 */

namespace Simettric\Sense\Controller;


abstract class AbstractInterceptor extends AbstractController
{

	protected static $executed = false;

    /**
     * @return bool
     */
    public abstract function canBeExecuted();

    /**
     * @param \WP_Query $query
     * @return void
     */
    public abstract function execute(\WP_Query $query);


    /**
     * @return bool
     */
    public function isExecuted(){

		$executed = static::$executed;

		if(!static::$executed)
			static::$executed = true;

		return $executed;

	}

}
