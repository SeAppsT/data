<?php


namespace modules\chat\models;


use Core\ORM\Model;

class Message extends Model {
    protected $table = 'messages';

    public $text;
    public $time;
    public $chat_id;
    public $sender_type;
}