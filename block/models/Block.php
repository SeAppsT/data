<?php

namespace modules\block\models;

use Core\ORM\Model;

class Block extends Model{
    protected $table = 'blocks';

    public $parent_id;
    public $user_id;
    public $name;
    public $type;
    public $access_needed;
    public $isShow;


    public function isValid(){
       if ($this -> name != ''
       && in_array($this -> access_needed, [1, 2, 3])
       && $this -> parent_id != ''
       && $this -> user_id != '')
           return true;
       else
           return false;
    }
    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getAccessNeeded()
    {
        return $this->access_needed;
    }

    /**
     * @param mixed $access_needed
     */
    public function setAccessNeeded($access_needed): void
    {
        $this->access_needed = $access_needed;
    }


}