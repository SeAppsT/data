<?php


namespace modules\chat\models;


use Core\ORM\Model;

class Chat extends Model {
    protected $table = 'chats';

    public $status;
    public $user_id;
}