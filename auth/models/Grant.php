<?php


namespace modules\auth\models;


use Core\ORM\Model;

class Grant extends Model{
    protected $table = 'grants';

    public $user_id;
    public $block_id;
    public $privilege_id;
    public $access_level;

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
    public function getBlockId()
    {
        return $this->block_id;
    }

    /**
     * @param mixed $block_id
     */
    public function setBlockId($block_id): void
    {
        $this->block_id = $block_id;
    }

    /**
     * @return mixed
     */
    public function getPrivilegeId()
    {
        return $this->privilege_id;
    }

    /**
     * @param mixed $privilege_id
     */
    public function setPrivilegeId($privilege_id): void
    {
        $this->privilege_id = $privilege_id;
    }

    /**
     * @return mixed
     */
    public function getAccessLevel()
    {
        return $this->access_level;
    }

    /**
     * @param mixed $access_level
     */
    public function setAccessLevel($access_level): void
    {
        $this->access_level = $access_level;
    }


}