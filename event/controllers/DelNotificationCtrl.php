<?php

use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\auth\models\Notification;
use modules\main\repositories\EventRepository;

class DelNotificationCtrl extends Controller{
    protected $path = 'notification/del/*';
    public function stand(Request $request, Response $response){
        $eventRepo = new EventRepository();
        $delOpn = $eventRepo -> createNotificationRepo() -> del();
        if (getUrl(2) != 'all'){
            $delOpn -> where('id', equals(getUrl(2)));
        }
        $delOpn -> where('user_id', equals($response -> getSession('user')['id']));
        $delOpn -> go();
        header('Location: /notifications/show');
    }
}