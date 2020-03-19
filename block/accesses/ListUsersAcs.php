<?php


namespace accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use modules\main\accesses\BaseAcs;

class ListUsersAcs implements Accessable{

    public function checkAccess(Request $request, Response $response){
        $base = new BaseAcs();
        $base -> isAuth();
        return $base -> hasGrant('seeGrants', getUrl(2));
    }
}