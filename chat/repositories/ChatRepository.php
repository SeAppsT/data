<?php


namespace modules\chat\repositories;


use Core\ORM\Repository;
use modules\chat\models\Chat;

class ChatRepository extends Repository {
    protected $model = Chat::class;
}