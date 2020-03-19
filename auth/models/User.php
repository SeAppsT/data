<?php


namespace modules\auth\models;
use Core\ORM\Model;

class User extends Model{
    protected $table = 'users';

    public $name;
    public $code;
    public $active;

    public function generateCode(){
        $this -> code = rand(100000, 999999);
        $userRepo = new \auth\repositories\UserRepository();
        $users = $userRepo -> get('code') -> go();
        $res = true;
        foreach ($users as $checkUser){
            if ($checkUser -> code == $this -> code){
                $res = false;
            }
        }
        if ($res == false)
            $this -> generateCode();
    }
}