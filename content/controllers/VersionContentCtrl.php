<?php

use auth\repositories\UserRepository;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use Core\ORM\Repository;
use modules\block\models\VersionContent;

class VersionContentCtrl extends Controller{

    protected $path = 'content/versions/*';
    protected $access = \modules\content\accesses\VersionContentAcs::class;

    public function stand(Request $request, Response $response){
        $fileRepo = new \modules\content\repositories\FileRepository();
        $userRepo = new UserRepository();
        $repo = new Repository(new VersionContent());
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $content = $blockRepo -> createContentRepo()
            -> one()
            -> where('id', equals(getUrl(2)))
            -> belongsEntity('content_id', $repo -> get()
                -> where('content_id', equals(getUrl(2)))
                -> foreignKey('user_id', $userRepo -> one(), 'id')
                -> belongsEntity('version_contents_id', $fileRepo -> get(), 'id')
                -> orderBy('id')
                -> desc(), 'id')
            -> go();
        return $response -> page('versionContent', ['content' => $content]);
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}