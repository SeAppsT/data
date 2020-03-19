<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class LogoutCtrl extends Controller{

    protected $path = 'auth/logout';
    

    public function stand(Request $request, Response $response){
        $response -> delAllSession();
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
}