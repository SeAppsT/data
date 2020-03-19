<?php




use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class EditBlockCtrl extends Controller{

    protected $access = \accesses\EditBlockAcs::class;

    protected $path = 'block/edit/*';

    public function stand(Request $request, Response $response){
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $block = $blockRepo -> one()
            -> where('id', equals(getUrl(2)))
            -> foreignKey('parent_id', $blockRepo -> one('name'), 'id')
            -> go();
        $blocks = $blockRepo -> getAvailableBlocksForAdd($response -> getSession('user')['id'], $block -> getAccessNeeded());
        print_r($blocks);
        return $response -> page('addBlock', ['block' => $block, 'blocks' => $blocks]);
    }

    public function action(Request $request, Response $response){
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $block = $blockRepo -> one()
            -> where('id', equals(getUrl(2)))
            -> go();
        $block = $blockRepo -> getUserData($request, $block);
        if ($block -> isValid()){
            \Core\Config::getMapper('main') -> update($block);
            $eventRepo = new \modules\main\repositories\EventRepository();
            $eventRepo -> createEntityEvent('edit', $block -> getTable(), $block -> id);
            header('Location: /block/show/'.$block -> id);
        }
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}