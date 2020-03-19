<?php


use auth\repositories\UserRepository;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class DelUserCtrl extends Controller{

    protected $path = 'user/del/*';

    public function stand(Request $request, Response $response){
        $userRepo = new UserRepository();
        $userRepo -> delUser(getUrl(2));
        $response -> delAllSession();
        header('Location: /auth/login');
    }
}