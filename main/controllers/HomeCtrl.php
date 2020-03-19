<?php


use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\main\repositories\BookmarkRepository;

class HomeCtrl extends Controller{

    protected $path = 'home';
    protected $access = \modules\auth\accesses\ProfileAcs::class;

    public function stand(Request $request, Response $response){
        $bookmarkRepo = new BookmarkRepository();
        $blockRepo = new \modules\block\repositories\BlockRepository();
        $bookmarks = $bookmarkRepo -> get()
            -> where('user_id', equals($response -> getSession('user')['id']))
            -> foreignKey('block_id', $blockRepo -> one(), 'id')
            -> go();
        return $response -> page('home', ['bookmarks' => $bookmarks, 'request' => $request]);
    }
}