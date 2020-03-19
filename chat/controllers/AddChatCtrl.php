<?php

use Core\Config;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\chat\models\Chat;

class AddChatCtrl extends Controller {

    protected $path = 'chat/add';

    public function stand(Request $request, Response $response){
        $chat = new Chat();
        $chat -> user_id = $response -> getSession('user')['id'];
        $chat -> status = 'opened';
        Config::getMapper('main') -> insert($chat);
        //header('Location: /help/'.$chat -> id);
    }
}