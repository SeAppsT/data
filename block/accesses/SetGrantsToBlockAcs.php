<?php


namespace accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use modules\block\repositories\BlockRepository;
use modules\main\accesses\BaseAcs;

class SetGrantsToBlockAcs implements Accessable {

    public function checkAccess(Request $request, Response $response){
        $base = new BaseAcs();
        $base -> isAuth();
        $blockRepo = new BlockRepository();
        if ($request -> isGet())
            return $base -> hasGrant('setGrants', getUrl(2));
        else{
            return $base -> hasGrant('setGrants', getUrl(2));
        }

    }
}