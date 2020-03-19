<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use Core\ORM\Repository;
use modules\block\models\VersionContent;

class ResetContentCtrl extends Controller{

    protected $path = 'content/reset/*';
    protected $access = \modules\content\accesses\VersionContentAcs::class;

    public function stand(Request $request, Response $response){
        $repo = new Repository(new VersionContent());
        $content_id = $repo -> one('content_id')
            -> where('id', equals(getUrl(2)))
            -> go() -> content_id;
        $repo -> upd()
            -> set('current', 0)
            -> where('current', equals(1))
            -> where('content_id', equals($content_id))
            -> go();
        $repo -> upd()
            -> set('current', 1)
            -> where('id', equals(getUrl(2)))
            -> go();
        header('Location:'.$_SERVER['HTTP_REFERER']);
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}