<?php

use modules\block\models\VersionContent;
use modules\block\models\Content;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\block\repositories\BlockRepository;
use Core\Config;

class AddContentCtrl extends Controller{

    protected $path = 'content/add/*';
    protected $access = \modules\content\accesses\AddContentAcs::class;

    public function stand(Request $request, Response $response){
        $blockRepo = new BlockRepository();
        $block = $blockRepo -> one() -> where('id', equals(getUrl(2))) -> go();
        $blocks = $blockRepo -> getAvailableBlocks($response -> getSession('user')['id']);
        return $response -> page('addContent', ['block' => $block, 'type' => $request -> getQueryParam('type'), 'blocks' => $blocks]);
    }

    public function action(Request $request, Response $response){
        $error = '';
        $content = new Content();
        $content -> block_id = getUrl(2);
        $content -> type = $request -> getQueryParam('type');
        $content -> active = 1;
        $content -> access_needed = $request -> getPostParam('access_needed');
        $version_content = new VersionContent();
        $version_content -> content = $request -> getPostParam('content');
        if ($version_content -> content == ' ' || $version_content -> content == '  ')
            $version_content -> content = '//';
        $version_content -> title = $request -> getPostParam('title');
        $version_content -> user_id = $response -> getSession('user')['id'];
        $version_content -> current = 1;

        $version_content -> importance = $request -> getPostParam('importance');
        $eventRepo = new \modules\main\repositories\EventRepository();
        $mapper = Config::getMapper('main');

        if ($content -> isValid() && $version_content -> isValid()) {
            $mapper -> insert($content);
            $version_content -> content_id = $content -> id;
            $eventRepo -> createEntityEvent('add', $content -> getTable(), getUrl(2), $content -> id);
            $mapper -> insert($version_content);
            $fileRepo = new \modules\content\repositories\FileRepository();
            $fileRepo -> saveFiles($version_content -> id);
            //header('Location: /block/show/'.getUrl(2));
        }
        else
            return $response -> page('addContent', ['error' => $error]);
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}