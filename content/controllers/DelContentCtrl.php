<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\block\repositories\BlockRepository;

class DelContentCtrl extends Controller{

    protected $path = 'content/del/*';
    protected $access = \modules\content\accesses\DelContentAcs::class;

    public function stand(Request $request, Response $response){
        $blockRepo = new BlockRepository();
        $content = $blockRepo -> createContentRepo()
            -> one()
            -> where('id', equals(getUrl(2)))
            -> go();
        $eventRepo = new \modules\main\repositories\EventRepository();
        $eventRepo -> createEntityEvent('delete', $content -> getTable(), $content -> block_id, $content -> id);
        $content -> active = '0';
        \Core\Config::getMapper('main') -> update($content);
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}