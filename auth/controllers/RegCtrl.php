<?php

use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\auth\models\User;
use auth\repositories\UserRepository;
class RegCtrl extends Controller{

    protected $path = 'auth/reg';
    protected $access = modules\auth\accesses\NoAuthAcs::class;

    public function stand(Request $request, Response $response){
        return $response -> page('reg');
    }

    public function action(Request $request, Response $response){
        $userRepo = new UserRepository();
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $user = new User();
        $user -> name = $request -> getPostParam('nick');
        if ($request -> getPostParam('tester') == 1)
            $user -> property = 'tester';
        $user -> generateCode();
        $user -> active = 1;
        if ($user -> isValid()){
            $block = $blockRepo -> one()
                -> where('type', equals('main'))
                -> go();

            \Core\Config::getMapper('main') -> insert($user);
            $userRepo -> setBaseGrants($user -> id, $block -> id);
            $userRepo -> saveIp($user -> id);
            $response -> addSession('user', ['id' => $user -> id, 'name' => $user -> name, 'code' => $user -> code]);
            header('Location: /auth/profile');
        }
    }
}