<?php


namespace modules\main\models;


use Core\ORM\Model;

class Event extends Model{
    protected $table = 'events';

    public $event;
    public $user_id;
    public $block_id;
}