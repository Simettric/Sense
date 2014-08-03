<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 31/07/14
 * Time: 13:42
 */

namespace Sense\Model;



class UserModel {




    /**
     * @param null $user_id
     * @return WP_User
     */

    function getUser($user_id=null){

        /*
         * class WP_User
         * ID (int) - the user's ID.
         * caps (array) - the individual capabilities the user has been given.
         * cap_key (string) -
         * roles (array) - the roles the user is part of.
         * allcaps (array) - all capabilities the user has, including individual and role based.
         * first_name (string) - first name of the user.
         * last_name (string) - last name of the user.
        */

        return \get_userdata( ($user_id ? $user_id : \get_current_user_id()) );
    }

    /**
     * @param $field
     * @param $value
     * @return WP_User|false
     */
    function getUserBy( $field, $value ){
        return \get_user_by( $field, $value );
    }


    function authenticate($user, $password){


        $user = \wp_authenticate($user, $password);

        if($user instanceof \WP_User){
            \wp_set_current_user( $user->ID, $user->user_login );
            \wp_set_auth_cookie( $user->ID );
            \do_action( 'wp_login', $user->user_login );
        }else{
            return false;
        }
        return $user;
    }

} 