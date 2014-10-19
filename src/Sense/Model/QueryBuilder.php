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

    function search($search){
        $this->query_vars["s"] = $search;

        return $this;
    }

    function metaRelation($mode="AND"){
        $this->query_vars["meta_query"]["relation"] = $mode;
    }

    function whereMeta($key, $value, $comp="=", $type=null){


        $meta = array(
            'key'       => $key,
            'value'     => $value,
            'compare'   => $comp
        );

        if($type){
            $meta["type"] = $type;
        }
        $this->query_vars["meta_query"][] = $meta;

        return $this;
    }

    function taxonomyRelation($mode="AND"){
        $this->query_vars["tax_query"]["relation"] = $mode;
    }

    function whereTaxonomy($taxonomy, $terms, $comp="IN", $field="id"){


        $meta = array(
            'taxonomy'       => $taxonomy,
            'terms'     => $terms,
            'operator'   => $comp,
            'field'      => $field
        );

//        if($type){
//            $meta["type"] = $type;
//        }
        $this->query_vars["tax_query"][] = $meta;

        return $this;
    }

    function where($value, $type)
    {


        if(false !== in_array($type, array("author", "category"))){
            if(is_array($value)){
                $this->query_vars[$type . "__in"] = $value;
            }else{
                $this->query_vars[$type] = $value;
            }

        }else{
            $this->query_vars[$type] = $value;
        }




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

    function orderByMeta($key, $order="DESC", $numeric=false){
        $this->query_vars["orderby"] = 'meta_value';

        if($numeric){
            $this->query_vars["meta_key"] = $key;
        }else{
            $this->query_vars["meta_value_num"] = $key;
        }

        $this->query_vars["order"] = $order;
        return $this;
    }

    function getArray(){
        $query = $this->getWPQuery();
        $items = $query->get_posts();

        return is_array($items) ? $items : array();
    }

    function getWPQueryVars(){
        return $this->query_vars;
    }

    function getWPQuery(\WP_Query $query=null){

        if(!$query){
            return new \WP_Query($this->query_vars);
        }

        array_merge($query->query_vars, $this->query_vars);

        return $query;
    }


    /**
     * Only before pre get posts
     */
    function updateMainQuery(){
        $vars = $this->query_vars;





        add_action("pre_get_posts", function(\WP_Query $query) use($vars){



            if ( $query->is_main_query() ) {

                foreach($vars as $key=>$value){
                    $query->set( $key, $value );
                }

            }

        });
    }


} 