<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 1/07/14
 * Time: 23:40
 */

namespace Sense\Model;


use Symfony\Component\Config\Definition\Exception\Exception;

abstract class AbstractModel {


    protected $post_type_name;


    function getOneBy($field, $value, $type="post"){



        $items = $this->createQueryBuilder($type)->where($value, $field)->limit("1")->getArray();

        return count($items) ? $items[0] : null;
    }


    /**
     * @param array|string $types
     * @return QueryBuilder
     */
    function createQueryBuilder($types=null){


        if($types) $this->post_type_name = $types;


        $qb = new QueryBuilder();
        if($this->post_type_name){
            $qb->fromType($this->post_type_name);
        }

        return $qb;
    }


    function setMeta($post_id, $key, $value, $add=false){
        $add ? \add_post_meta($post_id, $key, $value) : \update_post_meta($post_id, $key, $value);
    }

    function getMeta($post_id, $key, $default=null, $single=true){
        $value = \get_post_meta($post_id, $key, $single);
        return $value!==""?$value:$default;
    }

    function removeMeta($post_id, $key, $value=null){
        return $value ? \delete_post_meta($post_id, $key, $value) : \delete_post_meta($post_id, $key);
    }


    function setExternalImageToPost($url, $post_id, $desc="", $set_thumbnail=false){

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $tmp        = \download_url( $url );
        $file_array = array();
        preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);

        if(!isset($matches[0])) return;

        $file_array['name']     =  basename($matches[0]);
        $file_array['tmp_name'] = $tmp;

        // If error storing temporarily, unlink
        if ( is_wp_error( $tmp ) ) {
            @unlink($file_array['tmp_name']);
            $file_array['tmp_name'] = '';
        }

        // do the validation and storage stuff
        $id = \media_handle_sideload( $file_array, $post_id, $desc ? $desc : $file_array['name'] );
        @unlink($file_array['tmp_name']);

        if ( \is_wp_error($id) ) {

        }

        if($set_thumbnail){
            \set_post_thumbnail( $post_id, $id );
        }

        return $id;

    }







    /**
     * @param $data
     * @return \WP_Post
     * @throws \Exception
     */
    function create($data){

          if(!isset($data["post_name"])){
              $data["post_name"] = \sanitize_title($data["post_title"] );
          }

          $post = \wp_insert_post($data);
          if(is_int($post)){
              $post = \get_post($post);
              \wp_update_post( $post );
              return $post;
          }

          /**
           * @var $post \WP_Error
           */
          throw new \Exception(implode(",",$post->get_error_messages()));
    }


    function update(\WP_Post $post, $data){



        $data["ID"] = $post->ID;


        $post_id = \wp_update_post($data);


        if(is_int($post_id)){
            return \get_post($post_id);
        }
        /**
         * @var $post \WP_Error
         */
        throw new \Exception(implode(",",$post->get_error_messages()));
    }

    function publish(\WP_Post $post){
        \wp_update_post($post);
        \wp_publish_post($post);
    }

    /**
     * @param \WP_Post $post
     * @param array $terms
     * @param string $taxonomy
     * @param bool $append
     * @return array
     * @throws \Exception
     */
    function addTaxonomyTerms( \WP_Post $post, array $terms, $taxonomy, $append=false ){

        foreach ($terms as $key => $var) {
            $terms[$key] = (int)$var;
        }

        $ids = \wp_set_object_terms( $post->ID, $terms, $taxonomy, $append );
        if($ids instanceof \WP_Error){
            throw new \Exception(implode(",",$post->get_error_messages()));
        }

        return $ids;
    }


    /**
     * @param $file
     * @param \WP_POST $attachment_for
     * @return null|array
     */
    function uploadToMediaLibrary(array $file, \WP_Post $attachment_for=null){
        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        $upload_overrides = array( 'test_form' => false );
        $file_array = wp_handle_upload( $file, $upload_overrides );


        if ( !$file_array || !isset($file_array["file"]) ) {
            return null;
        }

        // $filename should be the path to a file in the upload directory.
        $filename = $file_array["file"];


// Check the type of tile. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype( basename( $filename ), null );

// Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

// Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

// Insert the attachment.
        $attach_id = wp_insert_attachment( $attachment, $filename, $attachment_for? $attachment_for->ID : null );




// Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );



        return array(
            "file" => $filename,
            "url"  => $file_array["url"],
            "id"   => $attach_id
        );

    }


    function setPostThumbnail($post_id, $post_attachment_id){
        \set_post_thumbnail($post_id, $post_attachment_id);
    }

} 