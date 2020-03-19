<?php


namespace modules\content\accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use Core\ORM\Repository;
use modules\block\models\VersionContent;
use modules\block\repositories\BlockRepository;
use modules\main\accesses\BaseAcs;

class DelContentAcs implements Accessable {

    public function checkAccess(Request $request, Response $response){
        $base = new BaseAcs();
        $base -> isAuth();
        $blockRepo = new BlockRepository();
        $content = $blockRepo -> getContentById(getUrl(2));
        if ($base -> hasGrant('lookBlock', $content -> block_id, $content -> access_needed)) {
            if ($base -> hasDelAccessToContent($content)) {
                return true;
            } else {
                return false;
            }
        } else
            return false;
    }
}