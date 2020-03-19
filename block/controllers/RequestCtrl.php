<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\main\models\Event;
use modules\main\models\EventId;
use modules\auth\models\User;
use modules\block\models\Block;
use modules\main\repositories\EventRepository;

class RequestCtrl extends Controller{

    protected $path = 'block/request/*';

    public function stand(Request $request, Response $response){

        $eventRepo = new EventRepository();
        $blockRepo = new \modules\block\repositories\BlockRepository();

        $block = $blockRepo -> one()
            -> where('id', equals(getUrl(2)))
            -> go();

        if ($request -> getQueryParam('type') == 'request'){
            if (isAccess($block)) {
                $eventRepo -> getAccessEvent($block); // получить привилегию
            }
            else
                $eventRepo -> createRequestEvent($block); // сделать запрос
        }
        else if ($request -> getQueryParam('type') == 'approve')
            $eventRepo -> createAnswerEvent($request, 'approve');
        else if ($request -> getQueryParam('type') == 'reject')
            $eventRepo -> createAnswerEvent($request, 'reject');
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
}