<?php


use auth\repositories\UserRepository;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class ListUsersCtrl extends Controller{

    protected $path = 'block/users/*';
    protected $access = \accesses\ListUsersAcs::class;

    public function stand(Request $request, Response $response){
        $userRepo = new UserRepository();
        $users = $userRepo -> get() -> where('active', equals(1))
            -> belongsEntity('user_id', $userRepo -> createGrantRepo() -> get()
                -> where('block_id', equals(getUrl(2)))
                -> foreignKey('privilege_id', $userRepo -> createPrivilegeRepo() -> one(), 'id'), 'id')
            -> go();
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $block = $blockRepo -> one() -> where('id', equals(getUrl(2))) -> go();
        return $response -> page('listUsers', ['users' => $users, 'block' => $block]);
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}