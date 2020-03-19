<?php


namespace modules\content\accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use Core\ORM\Repository;
use modules\block\models\VersionContent;
use modules\block\repositories\BlockRepository;
use modules\main\accesses\BaseAcs;

class EditContentAcs implements Accessable{

    public function checkAccess(Request $request, Response $response){
        $base = new BaseAcs();
        $base -> isAuth();
        $blockRepo = new BlockRepository();
        $verconRepo = new Repository(new VersionContent());
        $content = $blockRepo -> getContentById(getUrl(2));
        if ($base -> hasEditAccessToContent($content)){
            if ($request -> isPost()){
                $logic = [];
                //$logic[] = $base -> hasGrant('addContent', $content -> block_id, $request->getPostParam('importance'));
                $logic[] = $base -> hasGrant('lookBlock', $content -> block_id, $request->getPostParam('access_needed'));
                if ($logic[0] == true){
                    return true;
                } else
                    return false;
            }
            return true;
        } else{
            return false;
        }
    }
}