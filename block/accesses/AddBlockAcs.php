<?php


namespace accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use modules\main\accesses\BaseAcs;

class AddBlockAcs implements Accessable {

    public function checkAccess(Request $request, Response $response){
        $base = new BaseAcs();
        $base -> isAuth();

        if ($request -> isGet())
            return $base -> hasGrant('addBlock', getUrl(2));
        else
            return $base -> hasGrant('addBlock', getUrl(2), $request -> getPostParam('access_needed'));
    }
}