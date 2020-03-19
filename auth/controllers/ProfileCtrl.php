<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class ProfileCtrl extends Controller{

    protected $path = 'auth/profile';
    protected $access = \modules\auth\accesses\ProfileAcs::class;

    public function stand(Request $request, Response $response){
        $userRepo = new \auth\repositories\UserRepository();
        if (getUrl(2) == '')
            $id = $response -> getSession('user')['id'];
        else
            $id = getUrl(2);
        $user = $userRepo -> one()
            -> where('id', equals($id))
            -> go();
        return $response -> page('profile', ['user' => $user]);
    }
}