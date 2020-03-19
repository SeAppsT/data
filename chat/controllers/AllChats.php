<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\chat\repositories\ChatRepository;

class AllChats extends Controller {

    protected $path = 'chat/all';

    public function stand(Request $request, Response $response){
        $chatRepo = new ChatRepository();
        $chats = $chatRepo -> get() -> go();
        return $response -> page('allChats', ['chats' => $chats]);
    }
}