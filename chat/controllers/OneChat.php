<?php

use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class OneChat extends Controller {

    protected $path = 'help/*';

    public function stand(Request $request, Response $response){
        $messageRepo = new \Core\ORM\Repository(new \modules\chat\models\Message());
        $chatRepo = new \modules\chat\repositories\ChatRepository();
        if (getUrl(1) == ''){
            $chat = $chatRepo -> one()
                -> where('user_id', equals($response -> getSession('user')['id']))
                -> belongsEntity('chat_id', $messageRepo -> get(), 'id')
                -> go();
            if (empty($chat)){
                $chat = new \modules\chat\models\Chat();
                $chat -> user_id = $response -> getSession('user')['id'];
                $chat -> status = 'opened';
                \Core\Config::getMapper('main') -> insert($chat);
                $chat -> messages = Array();
            }
        } else{
            $chat = $chatRepo -> one()
                -> where('user_id', equals(getUrl(1)))
                -> belongsEntity('chat_id', $messageRepo -> get(), 'id')
                -> go();
        }
        return $response -> page('oneChat', ['chat' => $chat]);
    }
}