<?php


namespace modules\block\repositories;


use auth\repositories\UserRepository;
use Core\Management\Request;
use Core\Management\Response;
use Core\ORM\Repository;
use modules\block\models\Block;
use modules\block\models\Content;
use modules\block\models\VersionContent;

class BlockRepository extends Repository {
    protected $model = Block::class;

    public function createContentRepo(){
        return new Repository(new Content());
    }

    public function getAvailableBlocks($user_id){
        return $this -> get('blocks.name', 'blocks.id')
            -> where('active', equals(1))
            -> join('grants', 'grants.block_id = blocks.id AND grants.privilege_id = 11381218 AND grants.user_id = '.$user_id)
            -> go();
    }

    public function getAvailableBlocksForAdd($user_id, $access_level = 3){
        return $this -> get('blocks.name', 'blocks.id')
            -> where('active', equals(1))
            -> join('grants', 'grants.block_id = blocks.id AND grants.privilege_id = 11381219 AND grants.access_level = '.$access_level.' AND grants.user_id = '.$user_id)
            -> go();
    }

    public function getAvailableBlocksForAddContent($user_id, $access_level = 3){
        return $this -> get('blocks.name', 'blocks.id')
            -> where('active', equals(1))
            -> join('grants', 'grants.block_id = blocks.id AND grants.privilege_id = 11381222 AND grants.access_level = '.$access_level.' AND grants.user_id = '.$user_id)
            -> go();
    }

    public function getUserData(Request $request, Block $block){
        if ($request -> getPostParam('parent_id') != '')
            $block -> setParentId($request -> getPostParam('parent_id'));
        // TODO: delete condition
        $block -> setAccessNeeded($request -> getPostParam('access_needed'));
        $block -> setName($request -> getPostParam('name'));
        if ($request -> getPostParam('show') == 1)
            $block -> isShow = '1';
        else
            $block -> isShow = '0';
        return $block;
    }

    public function getContentById($int){
        $verconRepo = new Repository(new VersionContent());
        $content = $this -> createContentRepo()
            -> one()
            -> where('id', equals($int))
            -> belongsEntity('content_id', $verconRepo -> one()
                -> where('current', equals(1)), 'id')
            -> go();
        return $content;
    }

    /*public function getContentData(Request $request, Response $response, VersionContent $version_content){
        if ($request -> getQueryParam('type') == 'image'){
            $version_content -> content = imgUpload($_FILES['content'], 'user_contents/');
        } else
            $version_content -> content = $request->getPostParam('content');

        $version_content -> title = $request->getPostParam('title');
        $version_content -> user_id = $response -> getSession('user')['id'];
        return $version_content;
    }*/

}