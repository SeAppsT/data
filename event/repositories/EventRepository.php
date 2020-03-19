<?php


namespace modules\main\repositories;


use auth\repositories\UserRepository;
use Core\Config;
use Core\Management\Request;
use Core\ORM\Repository;
use modules\auth\models\Grant;
use modules\auth\models\Notification;
use modules\block\models\Block;
use modules\block\repositories\BlockRepository;
use modules\main\models\Event;
use modules\main\models\EventId;

class EventRepository extends Repository{
    protected $model = Event::class;

    public function createNotificationRepo(){
        return new Repository(new Notification());
    }

    public function createEventIdRepo(){
        return new Repository(new EventId());
    }

    public function getAccessEvent(Block $block){
        $userRepo = new UserRepository();
        $blockRepo = new BlockRepository();

        $block -> parent_id = $blockRepo->one()
            ->where('id', equals($block -> parent_id))
            ->go();

        if ($block -> parent_id -> type == 'main'){
            $grant = new Grant();
            $grant->user_id = $_SESSION['user']['id'];
            $grant->block_id = $block->id;
            $grant->privilege_id = $userRepo->getPrivilegeId('lookBlock');
            $grant->access_level = 1;
            Config::getMapper('main')->insert($grant);
        } else{
            $grants = $userRepo -> createGrantRepo()
                -> get()
                -> where('user_id', equals($_SESSION['user']['id']))
                -> where('block_id', equals($block -> parent_id -> id))
                -> go();
            foreach ($grants as $tempGrant){
                $grant = $tempGrant;
                $grant -> block_id = $block -> id;
                Config::getMapper('main') -> insert($grant);
            }
        }
        header('Location: /block/show/'.$block -> id);
    }

    public function createRequestEvent(Block $block){
        $event = new Event();
        $event -> event = 'request';
        $event -> user_id = $_SESSION['user']['id'];
        $event -> block_id = $block -> id;
        Config::getMapper('main') -> insert($event);
        $this -> sendNotifications($event, 3);
    }

    public function createAnswerEvent(Request $request, $action){
        $event = new Event();
        $event -> event = $action;
        $event -> user_id = $_SESSION['user']['id'];
        $lastEvent = $this -> one()
            -> where('id', equals($request -> getUrl(2)))
            -> go();
        $event -> block_id = $lastEvent -> block_id;
        Config::getMapper('main') -> insert($event);
        $this -> createEventId($event -> id, $lastEvent -> getTable(), $lastEvent -> id);
        $this -> sendNotification($event, $lastEvent -> user_id);
        if ($action == 'approve') {
            $grant = new Grant();
            $grant->user_id = $lastEvent->user_id;
            $grant->block_id = $lastEvent->block_id;
            $userRepo = new UserRepository();
            $grant->privilege_id = $userRepo->getPrivilegeId('lookBlock');
            $grant -> access_level = 1;
            Config::getMapper('main') -> insert($grant);
        }
    }

    public function createEntityEvent($action, $table, $block_id, $id = null){
        $event = new Event();
        $event -> event = $action;
        $event -> user_id = $_SESSION['user']['id'];
        $event -> block_id = $block_id;
        Config::getMapper('main') -> insert($event);
        if ($table == 'contents')
            $this -> createEventId($event -> id, $table, $id);
        $this -> sendNotifications($event, 2);
    }

    private function sendNotification(Event $event, int $user_id){
        $notification = new Notification();
        $notification -> event_id = $event -> id;
        $notification -> user_id = $user_id;
        if ($_SESSION['user']['id'] == $user_id)
            $notification -> seen = 1;
        else
            $notification -> seen = '0';
        Config::getMapper('main') -> insert($notification);
    }

    public function sendNotifications(Event $event, $access_level){
        $userRepo = new UserRepository();
        $grants = $userRepo -> createGrantRepo()
            -> get()
            -> where('block_id', equals($event -> block_id))
            -> where('privilege_id', equals($userRepo -> getPrivilegeId('getNotifications')))
            -> where('access_level', bigEq(0))
            -> go();
        foreach ($grants as $grant){
            $this -> sendNotification($event, $grant -> user_id);
        }
    }
    // only after creating event
    private function createEventId($event_id, $table, $id){
        $eventId = new EventId();
        $eventId -> event_id = $event_id;
        $eventId -> entity_table = $table;
        $eventId -> entity_id = $id;
        Config::getMapper('main') -> insert($eventId);
    }
}