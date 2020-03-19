<?php


namespace modules\block\services;


use auth\repositories\UserRepository;
use Core\Management\Response;
use modules\block\models\Block;
use modules\block\repositories\BlockRepository;
use modules\content\repositories\FileRepository;
use modules\main\repositories\BookmarkRepository;

class ShowBlockService{
    static function getAllAvailableContents(Block $block, Response $response){
        $fileRepo = new FileRepository();
        $blockRepo = new BlockRepository();
        return $blockRepo
            -> createContentRepo()
            -> get('contents.id', 'version_contents.id AS vid', 'contents.type', 'contents.block_id', 'version_contents.content', 'version_contents.title', 'version_contents.importance', 'contents.access_needed', 'version_contents.current', 'version_contents.user_id', 'version_contents.date')
            -> join('version_contents', 'version_contents.content_id = contents.id AND version_contents.current = 1')
            -> where('contents.access_needed', letEq($response -> getSession('user')['grants']['lookBlock']))
            -> where('contents.block_id', equals($block -> id))
            -> where('contents.active', equals(1))
            -> belongsEntity('version_contents_id', $fileRepo -> get(), 'vid')
            -> go();
    }

    static function showBreadcrumbs(Block $block){
        $b = new \modules\block\models\Block();
        $b -> id = $block -> id;
        $b -> name = $block -> name;
        $b -> parent_id = $block -> parent_id;

        $blockRepo = new BlockRepository();
        $bread = Array($b);
        while (!empty(last_element($bread))) {
            $breadcrumb = $blockRepo->one('id', 'name', 'parent_id')
                -> where('id', equals(last_element($bread) -> parent_id))
                -> go();
            $bread[] = $breadcrumb;
        }
        $bread = array_reverse($bread);
        unset($bread[0]);
        return $bread;
    }

    static function processContents(Block $block){
        $blockRepo = new BlockRepository();
        foreach ($block -> contents as $content){
            $matches = [];
            preg_match_all("/@[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $content -> content, $matches, PREG_SET_ORDER);
            foreach ($matches as $match){
                $subBlock = $blockRepo -> one('id', 'name')
                    -> where('id', equals(explode('@', $match[0])[1]))
                    -> go();
                $content -> content = str_replace($match[0], '<a href="/block/show/'.$subBlock -> id.'" data-tooltip="'.$subBlock -> name.'">'.$match[0].'</a>', $content -> content);
            }
        }
    }

    static function getUserBlocks(Block $block, Response $response){
        $blockRepo = new BlockRepository();

        $block -> blocks = $blockRepo -> get('blocks.id', 'blocks.user_id', 'blocks.name', 'blocks.access_needed')
            -> where('active', equals(1))
            -> where('parent_id', equals($block -> id))
            -> join('grants', 'grants.block_id = blocks.id AND grants.privilege_id = 11381218 AND grants.user_id = '.$response -> getSession('user')['id'])
            -> orderBy('blocks.id')
            -> desc()
            -> go();
    }

    static function getMoreBlocks(Block $block, Response $response){
        $blockRepo = new BlockRepository();
        $num = $response -> getSession('user')['grants']['lookBlock'];

        $blocksLight = $blockRepo -> get()
            -> where('active', equals(1))
            -> where('access_needed', letEq($num))
            -> where('parent_id', equals($block->id))
            -> orderBy('id')
            -> desc()
            -> go();
        $ids = [];
        if (!empty($block -> blocks)) {
            foreach ($block->blocks as $miniBlock) {
                $ids[] = $miniBlock->id;
            }
        }
        if (!empty($blocksLight)) {
            foreach ($blocksLight as $blockLight) {
                if (!in_array($blockLight->id, $ids))
                    $block->blocks[] = $blockLight;
            }
        }
    }

    static function processBlocks(Block $block, Response $response){
        $userRepo = new UserRepository();
        foreach ($block -> blocks as $miniBlock){
            $miniBlock -> grant = $userRepo -> createGrantRepo()
                -> one()
                -> where('block_id', equals($miniBlock -> id))
                -> where('user_id', equals($response -> getSession('user')['id']))
                -> where('privilege_id', equals($userRepo -> getPrivilegeId('lookBlock')))
                -> go();
            if (empty($miniBlock -> grant)){
                $eventRepo = new \modules\main\repositories\EventRepository();
                $req = $eventRepo -> get()
                    -> where('block_id', equals($miniBlock -> id))
                    -> where('user_id', equals($response -> getSession('user')['id']))
                    -> belongsEntity('entity_id', $eventRepo -> createEventIdRepo()
                    -> one(), 'id')
                    -> orderBy('id')
                    -> desc()
                    -> go();
                if (empty($req))
                    $miniBlock -> request = 0;
                else {
                    if ($eventRepo -> createEventIdRepo() -> one()
                    -> where('entity_id', equals($req[0] -> id))
                    -> go() == null) {
                        $miniBlock -> request = 1;
                    } else{
                        $miniBlock -> request = 0;
                    }
                }
            }
        }
    }

    static function isBookmark(Block $block, Response $response, BookmarkRepository $bookmarkRepo){
        $block -> bookmark = $bookmarkRepo -> one()
            -> where('user_id', equals($response -> getSession('user')['id']))
            -> where('block_id', equals($block -> id))
            -> go();
        if ($block -> bookmark != null)
            $block -> bookmark -> date = explode(" ", $block -> bookmark -> date)[0];
    }
}