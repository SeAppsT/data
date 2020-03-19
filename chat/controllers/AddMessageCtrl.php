<?php

use Core\Config;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\chat\models\Message;

class AddMessageCtrl extends Controller {

    protected $path = 'message/add/*';

    public function action(Request $request, Response $response){
        $message = new Message();
        $message -> sender_type = $request -> getQueryParam('sender_type');
        $message -> chat_id = getUrl(2);
        $message -> text = $request -> getPostParam('text');
        Config::getMapper('main') -> insert($message);
    }
}