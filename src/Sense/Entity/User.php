<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 3/08/14
 * Time: 20:45
 */

namespace Sense\Entity;


class User {


    /**
     * @var
     */
    private $_instance;

    function __construct(\WP_User $user){
        $this->_instance = $user;
    }

    function getId(){
        return $this->_instance->ID;
    }

    function getFirstName(){
        return $this->_instance->first_name;
    }

    function getLastName(){
        return  $this->_instance->first_name;
    }

    function getEmail(){
        return $this->_instance->user_email;
    }

    function getLogin(){
        return $this->_instance->user_login;
    }

    function getPassword(){
        return  $this->_instance->user_pass;
    }

    function getDisplayName(){
        return $this->_instance->display_name;
    }

} 