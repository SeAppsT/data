<?php

use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class getMessagesCtrl extends Controller {

    protected $path = 'message/get/*';

    public function stand(Request $request, Response $response){
        $messageRepo = new \Core\ORM\Repository(new \modules\chat\models\Message());
        $chatRepo = new \modules\chat\repositories\ChatRepository();

        if (!empty(getUrl(2))) {
            $messages = $chatRepo -> one()
                -> where('user_id', equals(getUrl(2)))
                -> belongsEntity('chat_id', $messageRepo -> get(), 'id')
                -> go();
                if (empty($messages)) {
                    $messages = array(
                        'status'=> 'error',
                        'type' => 'Чат не существует'
                    );
                }
        } else {
            $messages = array(
                'status'=> 'error',
                'type' => 'ID не задан'
            );
        }
        return $response -> json($messages);
    }
    
}