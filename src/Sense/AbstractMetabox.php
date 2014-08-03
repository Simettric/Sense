<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 26/07/14
 * Time: 17:58
 */

namespace Sense;


use Sense\Model\AbstractModel;

abstract class AbstractMetabox {

    /**
     * @var Model\AbstractModel
     */
    protected $_model;
    /**
     * @var Sense
     */
    protected $_container;

    function __construct(AbstractModel $model, Sense $container=null){

        $this->_model     = $model;
        $this->_container = $container;


        \add_action( 'add_meta_boxes', array($this, "setUp") );
        \add_action( 'save_post', array($this, "save") );
    }


    /**
     * @return AbstractModel
     */
    function getModel(){
        return $this->_model;
    }

    /**
     * @return Sense
     */
    function getContainer(){
        return $this->_container;
    }

    abstract function setUp();

    abstract function save($post_id);

} 