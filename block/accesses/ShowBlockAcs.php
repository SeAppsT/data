<?php


namespace accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use modules\block\repositories\BlockRepository;
use modules\main\accesses\BaseAcs;

class ShowBlockAcs implements Accessable {
    public function checkAccess(Request $request, Response $response){
        $blockRepo = new BlockRepository();
        $base = new BaseAcs();
        $base -> isAuth();
        if (getUrl(2) != 'main')
            $num = getUrl(2);
        else
            $num = $blockRepo -> one()
                -> where('type', equals('main'))
                -> go() -> id;
        return $base -> hasGrant('lookBlock', $num);
    }
}