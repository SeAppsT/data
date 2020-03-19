<?php


namespace modules\content\accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use modules\block\repositories\BlockRepository;
use modules\main\accesses\BaseAcs;

class AddContentAcs implements Accessable{

    public function checkAccess(Request $request, Response $response){
        $base = new BaseAcs();
        $base -> isAuth();

        $blockRepo = new BlockRepository();
        if ($request -> isGet())
            return $base -> hasGrant('addContent', getUrl(2));
        else {
            $logic = [];
            $logic[] = $base -> hasGrant('addContent', getUrl(2), $request -> getPostParam('importance'));
            $logic[] = $base -> hasGrant('lookBlock', getUrl(2), $request -> getPostParam('access_needed'));
            if ($logic[0] == true && $logic[1] == true){
                return true;
            } else
                return false;
        }
    }
}