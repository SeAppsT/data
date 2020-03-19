<?php


use Core\Config;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use modules\main\models\Bookmark;
use modules\main\repositories\BookmarkRepository;

class BookmarkCtrl extends Controller{

    protected $path = 'bookmark/*';
    protected $access = modules\main\accesses\BookmarkAcs::class;

    public function stand(Request $request, Response $response){
        $bookmarkRepo = new BookmarkRepository();
        if ($bookmarkRepo -> findBookmark(getUrl(1), $response -> getSession('user')['id']) == null){
            $bookmark = new Bookmark();
            $bookmark -> block_id = getUrl(1);
            $bookmark -> user_id = $response -> getSession('user')['id'];
            Config::getMapper('main') -> insert($bookmark);
        } else{
            $bookmarkRepo -> del()
                -> where('user_id', equals($response -> getSession('user')['id']))
                -> where('block_id', equals(getUrl(1)))
                -> go();
        }
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }

    public function forbidden(Request $request, Response $response){
        $response -> page('403');
    }
}