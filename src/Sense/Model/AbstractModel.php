<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 1/07/14
 * Time: 23:40
 */

namespace Sense\Model;


abstract class AbstractModel {


    protected $post_type_name;


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


    function setMeta($post_id, $key, $value){
        \update_post_meta($post_id, $key, $value);
    }

    function getMeta($post_id, $key, $default=null){
        $value = \get_post_meta($post_id, $key, true);
        return $value!==""?$value:$default;
    }

    function removeMeta($post_id, $key){
        return \delete_post_meta($post_id, $key);
    }


    function setExternalImageToPost($url, $post_id, $desc="", $set_thumbnail=false){

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $tmp        = \download_url( $url );
        $file_array = array();
        preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
        $file_array['name']     = basename($matches[0]);
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


    function getOEmbedHtml($post_id, $meta_key, $width, $height){
        if($data = $this->getMeta($post_id, $meta_key)){
            $html = \preg_replace('/(<*[^>]*width=)"[^>]+"([^>]*>)/', '\1"' . $width . '"\2', $data->html);
            return \preg_replace('/(<*[^>]*height=)"[^>]+"([^>]*>)/', '\1"' . $height . '"\2', $html);
        }
    }

//    function uploadFile(array $file){
//        $result = \wp_handle_upload($file);
//        return isset($result["file"]) ? $result["file"] : null;
//    }
//
//    function setThumbnailToPost($file_src, $post_id){
//        require_once( ABSPATH . 'wp-admin/includes/image.php' );
//        require_once( ABSPATH . 'wp-admin/includes/file.php' );
//        require_once( ABSPATH . 'wp-admin/includes/media.php' );
//
//
//        if($file_src){
//            if($attach_id = \wp_insert_attachment( null, $file_src, 0 )){
//                $attach_data = \wp_generate_attachment_metadata( $attach_id, $file_src );
//                \wp_update_attachment_metadata( $attach_id, $attach_data );
//                \set_post_thumbnail( $post_id, $attach_id );
//            }
//
//        }
//    }


} 