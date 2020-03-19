<?php


namespace modules\auth\models;


use Core\ORM\Model;

class Ip extends Model{
    protected $table = 'ips';

    public $user_id;
    public $ip;
    public $date;
}