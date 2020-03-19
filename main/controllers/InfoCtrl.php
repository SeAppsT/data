<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class InfoCtrl extends Controller{

    protected $path = 'info';

    public function stand(Request $request, Response $response){
        return $response -> page('info');
    }
}