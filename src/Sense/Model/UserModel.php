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

        return $this->getUserBy( "id", ($user_id ? $user_id : \get_current_user_id()) );
    }


    function toArray(\WP_User $user){
        $data = get_object_vars($user->data);
        $data["first_name"]  = $user->first_name;
        $data["last_name"]   = $user->last_name;
        $data["description"] = $user->description;
        return $data;
    }

    function getUserByEmail($value){
        return $this->getUserBy("email", $value);
    }

    /**
     * @param $field
     * @param $value
     * @return WP_User|false
     */
    function getUserBy( $field, $value ){
        return \get_user_by( $field, $value );
    }


    function setFlash($key, $message){
        $_SESSION["flash"][$key] = $message;
    }

    function getFlash($key){


        $flash = null;

        if(isset($_SESSION["flash"][$key])){
            $flash = $_SESSION["flash"][$key];
            unset($_SESSION["flash"][$key]);
        }



        return $flash;
    }


    function authenticate($user, $password){


        if(\filter_var($user, FILTER_VALIDATE_EMAIL)){

            $user = $this->getUserBy("email", $user);
            if($user instanceof \WP_User){
                $user = $user->user_login;
            }

        }else{
        
            
        
        }

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

    function forceAuthenticate(\WP_User $user){
        \wp_set_current_user( $user->ID, $user->user_login );
        \wp_set_auth_cookie( $user->ID );
        \do_action( 'wp_login', $user->user_login );
    }


    function createAndRegister($username, $password, $email){

        $user_id = \wp_create_user( $username, $password, $email );
        \wp_set_password( $password, $user_id );
        \wp_new_user_notification( $user_id, $password );

        return $this->getUser($user_id);
    }


    function updateUser(\WP_User $user, array $data){

        $data["ID"] = $user->ID;



        $user_id = \wp_update_user( $data );


        if ( !is_wp_error( $user_id ) ) {
            return true;
        }

        return false;

    }

    function changePassword($password, \WP_User $user){
        \wp_set_password( $password, $user->ID );
    }



    function uploadUserAvatarToMediaLibrary(\WP_User $user,array $file_data){
        if ( ! function_exists( 'wp_handle_upload' ) )
                require_once( ABSPATH . 'wp-admin/includes/file.php' );



            // An associative array with allowed MIME types.
            $mimes = array(
                'bmp'  => 'image/bmp',
                'gif'  => 'image/gif',
                'jpe'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg'  => 'image/jpeg',
                'png'  => 'image/png',
                'tif'  => 'image/tiff',
                'tiff' => 'image/tiff'
            );

            // An associative array to override default variables.
            $overrides = array(
                'mimes'     => $mimes,
                'test_form' => false
            );



            // Handles PHP uploads in WordPress.
            $avatar = \wp_handle_upload( $file_data, $overrides );

            if ( isset( $avatar['error'] ) )
                // Kills WordPress execution and displays HTML error message.
                \wp_die( $avatar['error'],  __( 'Image Upload Error', 'sense' ) );


            // An associative array about the attachment.
            $attachment = array(
                'guid'           => $avatar['url'],
                'post_content'   => $avatar['url'],
                'post_mime_type' => $avatar['type'],
                'post_title'     => basename( $avatar['file'] )
            );

            // Inserts the attachment into the media library.
            $attachment_id = \wp_insert_attachment( $attachment, $avatar['file'] );

// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
            // Generates metadata for the attachment.
            $attachment_metadata = \wp_generate_attachment_metadata( $attachment_id, $avatar['file'] );

            // Updates metadata for the attachment.
          \wp_update_attachment_metadata( $attachment_id, $attachment_metadata );



            // Updates user meta fields based on user ID.
            update_user_meta( $user->ID, '_avatar_type', 'custom' );
            update_user_meta( $user->ID, '_custom_avatar', $attachment_id );

    }

    function getUserAvatarUrlFromMediaLibrary(\WP_User $user, $size){

        if($array = \wp_get_attachment_image_src(get_user_meta( $user->ID, '_custom_avatar', true), $size)){
            return isset($array[0]) ? $array[0] : null;
        }
    }


    function sendRetrievePasswordLink($url, $user_login, \WP_User $user){

        global $wpdb, $wp_hasher;
        /**
         * Fires before a new password is retrieved.
         *
         * @since 1.5.0
         * @deprecated 1.5.1 Misspelled. Use 'retrieve_password' hook instead.
         *
         * @param string $user_login The user login name.
         */
        do_action( 'retreive_password', $user_login );
        /**
         * Fires before a new password is retrieved.
         *
         * @since 1.5.1
         *
         * @param string $user_login The user login name.
         */
        do_action( 'retrieve_password', $user_login );

        /**
         * Filter whether to allow a password to be reset.
         *
         * @since 2.7.0
         *
         * @param bool true           Whether to allow the password to be reset. Default true.
         * @param int  $user_data->ID The ID of the user attempting to reset a password.
         */
        $allow = apply_filters( 'allow_password_reset', true, $user->ID );

        if ( ! $allow )
            return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
        else if ( is_wp_error($allow) )
            return $allow;

        // Generate something random for a password reset key.
        $key = wp_generate_password( 20, false );

        /**
         * Fires when a password reset key is generated.
         *
         * @since 2.5.0
         *
         * @param string $user_login The username for the user.
         * @param string $key        The generated password reset key.
         */
        do_action( 'retrieve_password_key', $user_login, $key );

        // Now insert the key, hashed, into the DB.
        if ( empty( $wp_hasher ) ) {
            require_once ABSPATH . 'wp-includes/class-phpass.php';
            $wp_hasher = new \PasswordHash( 8, true );
        }
        $hashed = $wp_hasher->HashPassword( $key );
        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

        $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
        $message .= network_home_url( '/' ) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
        $message .= '<' . network_site_url($url . "?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

        if ( is_multisite() )
            $blogname = $GLOBALS['current_site']->site_name;
        else
            // The blogname option is escaped with esc_html on the way into the database in sanitize_option
            // we want to reverse this for the plain text arena of emails.
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $title = sprintf( __('[%s] Password Reset'), $blogname );

        /**
         * Filter the subject of the password reset email.
         *
         * @since 2.8.0
         *
         * @param string $title Default email title.
         */
        $title = apply_filters( 'retrieve_password_title', $title );
        /**
         * Filter the message body of the password reset mail.
         *
         * @since 2.8.0
         *
         * @param string $message Default mail message.
         * @param string $key     The activation key.
         */
        $message = apply_filters( 'retrieve_password_message', $message, $key );

        if ( $message && !wp_mail( $user->user_email, wp_specialchars_decode( $title ), $message ) )
            wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );

    }
} 