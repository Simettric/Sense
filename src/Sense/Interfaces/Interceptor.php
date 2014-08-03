<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 26/07/14
 * Time: 17:41
 */

namespace Sense\Interfaces;


interface Interceptor {


    function execute(\WP_Query $query);


} 