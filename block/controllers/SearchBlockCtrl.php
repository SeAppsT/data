<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\block\repositories\BlockRepository;

class SearchBlockCtrl extends Controller{

    protected $path = 'search';

    public function stand(Request $request, Response $response){
        $userRepo = new \auth\repositories\UserRepository();
        $users = $userRepo -> get()
            -> where('active', equals(1))
            -> go();
        $blockRepo = new BlockRepository();
        $blocks = $blockRepo -> get()
            -> where('active', equals(1))
            -> where('isShow', equals(1))
            -> go();
        return $response -> page('searchBlock', ['blocks' => $blocks, 'users' => $users]);
    }

    public function action(Request $request, Response $response){
        $blockRepo = new BlockRepository();
        $userRepo = new \auth\repositories\UserRepository();
        $block = $blockRepo -> one()
            -> where('id', equals($request -> getPostParam('block-search')))
            -> where('isShow', equals(1))
            -> foreignKey('user_id', $userRepo -> one(), 'id')
            -> go();
        if ($block == null)
            return $response -> page('404', ['description' => 'Такого блока нет или он неактивен']);
        $block -> grant = $userRepo -> createGrantRepo()
            -> one()
            -> where('block_id', equals($block -> id))
            -> where('user_id', equals($response -> getSession('user')['id']))
            -> where('privilege_id', equals($userRepo -> getPrivilegeId('lookBlock')))
            -> go();
        return $response -> page('searchBlockResults', ['block' => $block]);
    }
}