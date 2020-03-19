<?php


namespace modules\auth\accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use modules\main\accesses\BaseAcs;

class ProfileAcs implements Accessable{

    public function checkAccess(Request $request, Response $response){
        $base = new BaseAcs();
        return $base -> isAuth();
    }
}