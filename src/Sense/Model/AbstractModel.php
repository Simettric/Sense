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

    function createQueryBuilder(){

        $qb = new QueryBuilder();
        if($this->post_type_name){
            $qb->fromType($this->post_type_name);
        }

        return $qb;
    }


    function setMeta($post_id, $key, $value){
        \update_post_meta($post_id, $key, $value);
    }

    function getMeta($post_id, $key){
        $value = \get_post_meta($post_id, $key, true);
        return $value!==""?$value:null;
    }

    function removeMeta($post_id, $key){
        return \delete_post_meta($post_id, $key);
    }
} 