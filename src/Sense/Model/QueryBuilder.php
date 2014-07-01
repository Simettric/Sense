<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 2/07/14
 * Time: 0:01
 */

namespace Sense\Model;


class QueryBuilder {


    private $query_vars = array();

    /**
     * @param array|string $type
     * @return $this
     */
    function fromType($type){
        $this->query_vars["post_type"] = $type;
        return $this;
    }

    function whereMeta($key, $value, $comp="=", $type="CHAR"){
        $this->query_vars["meta_query"][] = array(
            'key'       => $key,
            'value'     => $value,
            'compare'   => $comp,
            'type'      => $type

        );
        return $this;
    }


    function offset($offset=0){
        $this->query_vars["offset"] = $offset;
        return $this;
    }

    function page($page){
        $this->query_vars["paged"] = $page;
        return $this;
    }

    function limit($limit=null){
        $this->query_vars["posts_per_page"] = $limit;
        return $this;
    }

    function random(){
        $this->query_vars["orderby"] = "rand";
        return $this;
    }

    function orderBy($column, $order="DESC"){
        $this->query_vars["orderby"] = $column;
        $this->query_vars["order"] = $order;
        return $this;
    }

    function orderByMeta($key, $order="DESC"){
        $this->query_vars["orderby"] = 'meta_value';
        $this->query_vars["meta_key"] = $key;
        $this->query_vars["order"] = $order;
        return $this;
    }

    function getArray(){
        $query = $this->getWPQuery();
        return $query->get_posts();
    }

    function getWPQueryVars(){
        return $this->query_vars;
    }

    function getWPQuery(){
        return new \WP_Query($this->query_vars);
    }


} 