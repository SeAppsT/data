<?php


//namespace Main\Controllers;
use accesses\ShowBlockAcs;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\block\repositories\BlockRepository;
use modules\block\models\Content;
use modules\auth\models\Grant;
use core\Config;
use modules\block\services\ShowBlockService;

class ShowBlockCtrl extends Controller{
    protected $path = 'block/show/*';
    protected $access = ShowBlockAcs::class;

    public function stand(Request $request, Response $response){
        $blockRepo = new BlockRepository();
        $userRepo = new \auth\repositories\UserRepository();
        $bookmarkRepo = new \modules\main\repositories\BookmarkRepository();
        if (getUrl(2) == 'main')
            $field = 'type';
        else
            $field = 'id';

        $block = $blockRepo -> one()
            -> where('active', equals(1))
            -> where($field, equals(getUrl(2)))
            -> go();

        if (empty($block))
            return $response -> page('404', ['description' => 'Данный блок не существует или неактивен']);
        $userRepo -> setGrants($block, $response);
        ShowBlockService::isBookmark($block, $response, $bookmarkRepo);
        $bread = ShowBlockService::showBreadcrumbs($block);
        $block -> contents = ShowBlockService::getAllAvailableContents($block, $response);

        ShowBlockService::getUserBlocks($block, $response);
        if ($block -> type != 'main' || $response -> getSession('user')['grants']['lookBlock'] != 1)
            ShowBlockService::getMoreBlocks($block, $response);

        ShowBlockService::processBlocks($block, $response);
        ShowBlockService::processContents($block);
        //$userRepo -> createFullGrants(2, $block -> id, 3);
        return $response -> page('showBlock', ['block' => $block, 'bread' => $bread, 'base' => new \modules\main\accesses\BaseAcs()]);
    }

    public function forbidden(Request $request, Response $response){
        $response -> page('403');
    }
}
