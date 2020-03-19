<?php

use Core\Config;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\chat\models\Chat;
use modules\chat\repositories\ChatRepository;

class ChangeChatStatusCtrl extends Controller {

    protected $path = 'chat/status/*';

    public function stand(Request $request, Response $response){
        $chatRepo = new ChatRepository();
        $chat = $chatRepo -> one()
            -> where('id', equals(getUrl(2)))
            -> go();
        $chat -> status = $request -> getQueryParam('status');
        Config::getMapper('main') -> update($chat);
        //header('Location: '.$_SERVER['HTTP_REFERER']);
    }
}