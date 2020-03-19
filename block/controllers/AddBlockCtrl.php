<?php


//namespace Main\Controllers;
use auth\repositories\UserRepository;
use Core\Config;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\block\models\Block;
use modules\block\repositories\BlockRepository;

class AddBlockCtrl extends Controller{

    protected $access = \accesses\AddBlockAcs::class;
    protected $path = 'block/add/*';

    public function stand(Request $request, Response $response){
        $blockRepo = new BlockRepository();
        $block = $blockRepo -> one()
            -> where('id', equals(getUrl(2)))
            -> foreignKey('parent_id', $blockRepo -> one('name'), 'id')
            -> go();
        return $response -> page('addBlock', ['block' => $block]);
    }
    public function action(Request $request, Response $response){
        $error = '';
        $block = new Block();
        $block -> setParentId($request -> getUrl(2));
        $block -> setUserId($response -> getSession('user')['id']);
        $block -> active = 1;
        $blockRepo = new BlockRepository();
        $block = $blockRepo -> getUserData($request, $block);
        if ($block -> isValid()){
            Config::getMapper('main') -> insert($block);
            $eventRepo = new \modules\main\repositories\EventRepository();
            $eventRepo -> createEntityEvent('add', $block -> getTable(), $block -> id);
            $userRepo = new UserRepository();
            $userRepo -> createFullGrants($response -> getSession('user')['id'], $block -> id, 3);
            header('Location: /block/show/'.$block -> id);
        } else {
            return $response -> page('addBlock', ['error' => $error, 'request' => $request]);
        }
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}