<?php


namespace modules\auth\models;


use Core\ORM\Model;

class Notification extends Model{
    protected $table = 'notifications';

    public $event_id;
    public $user_id;
}