<?php


namespace modules\content\accesses;


use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use Core\ORM\Repository;
use modules\block\models\VersionContent;
use modules\block\repositories\BlockRepository;
use modules\main\accesses\BaseAcs;

class VersionContentAcs implements Accessable{

    public function checkAccess(Request $request, Response $response){
        $base = new BaseAcs();
        $base -> isAuth();
        if (getUrl(1) == 'versions')
            $num = getUrl(2);
        else {
            $verconRepo = new Repository(new VersionContent());
            $num = $verconRepo -> one('content_id') -> where('id', equals(getUrl(2)))->go() -> content_id;
        }
        $blockRepo = new BlockRepository();
        $content = $blockRepo -> createContentRepo() -> one()
            -> where('id', equals($num))
            -> go();
        return $base -> hasGrant('seeVersionContent', $content -> block_id);
    }
}