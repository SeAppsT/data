<?php



//namespace Main\Controllers;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class DeleteBlockCtrl extends Controller{

    protected $access = \accesses\DelBlockAcs::class;

    protected $path = 'block/del/*';

    public function stand(Request $request, Response $response){
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $block = $blockRepo -> one()
            -> where('id', equals(getUrl(2)))
            -> go();
        $block -> active = '0';
        \Core\Config::getMapper('main') -> update($block);
        header('Location: /block/show/'.$block -> parent_id);
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}