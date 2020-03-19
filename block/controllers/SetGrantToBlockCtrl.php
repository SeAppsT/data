<?php


use auth\repositories\UserRepository;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;

class SetGrantToBlockCtrl extends Controller{

    protected $path = 'block/grant/*/*';
    protected $access = accesses\SetGrantsToBlockAcs::class;

    public function stand(Request $request, Response $response){
        $userRepo = new UserRepository();
        $privilegies = $userRepo -> createPrivilegeRepo()
            -> get()
            -> where('type', equals('public'))
            -> belongsEntity('privilege_id', $userRepo -> createGrantRepo()
                -> one()
                -> where('user_id', equals(getUrl(3)))
                -> where('block_id', equals(getUrl(2))), 'id')
            -> go();
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $block = $blockRepo -> one()
            -> where('id', equals(getUrl(2)))
            -> go();
        $block -> child = $blockRepo -> one()
            -> where('parent_id', equals($block -> id))
            -> go();
        $user = $userRepo -> one() -> where('id', equals(getUrl(3))) -> go();
        return $response -> page('setGrants', ['privilegies' => $privilegies, 'block' => $block, 'user' => $user]);
    }

    public function action(Request $request, Response $response){
        $userRepo = new UserRepository();
        $blockRepo = new \modules\block\repositories\BlockRepository();

        $block = $blockRepo -> one()
            -> where('id', equals(getUrl(2)))
            -> go();
        if ($request -> getPostParam('hierarсhy') == 1)
            $userRepo -> deleteGrants($block, getUrl(3));
        else
            $userRepo -> deleteBlockGrant($block -> id, getUrl(3));

        foreach ($_POST['bool'] as $key => $value){
            $grant = new \modules\auth\models\Grant();
            $grant -> user_id = getUrl(3);
            $grant -> privilege_id = $key;
            $grant -> block_id = $block -> id;
            $grant -> access_level = $_POST['input'][$key];
            if ($request -> getPostParam('hierarсhy') == 1)
                $userRepo -> createGrants($grant, $block);
            else
                $userRepo -> createBlockGrant($grant);
            //\Core\Config::getMapper('main') -> insert($grant);
        }
        header('Location: /block/users/'.getUrl(2));
    }

    public function forbidden(Request $request, Response $response){
        $response -> page('403-2');
    }
}