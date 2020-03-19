<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class ShowNotificationsCtrl extends Controller{

    protected $path = 'notifications/show';

    public function stand(Request $request, Response $response){
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $userRepo = new \auth\repositories\UserRepository();
        $eventRepo = new \modules\main\repositories\EventRepository();
        $notifications = $eventRepo -> createNotificationRepo()
            -> get()
            -> where('user_id', equals($response -> getSession('user')['id']))
            -> groupBy('id')
            -> desc()
            -> foreignKey('event_id', $eventRepo -> one()
                -> foreignKey('user_id', $userRepo -> one(), 'id')
                -> foreignKey('block_id', $blockRepo -> one(), 'id')
                -> belongsEntity('event_id', $eventRepo -> createEventIdRepo() -> one(), 'id')
                , 'id')
            -> go();
        foreach ($notifications as $notification){
            if ($notification -> event_id -> event == 'request') {
                $answer = $eventRepo->createEventIdRepo()
                    ->one()
                    ->where('entity_id', equals($notification->event_id->id))
                    ->go();
                if ($answer == false)
                    $notification->event_id->answered = 0;
                else
                    $notification->event_id->answered = 1;
            }
        }
        $eventRepo -> createNotificationRepo()
            -> upd()
            -> set('seen', 1)
            -> where('user_id', equals($response -> getSession('user')['id']))
            -> go();
        return $response -> page('showNotifications', ['notifications' => $notifications, 'request' => $request]);
    }
}