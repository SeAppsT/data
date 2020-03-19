<?php


namespace modules\main\accesses;


use auth\repositories\UserRepository;
use Core\Management\Accessable;
use Core\Management\Request;
use Core\Management\Response;
use modules\main\repositories\BookmarkRepository;

class BookmarkAcs implements Accessable{

    public function checkAccess(Request $request, Response $response){
        $bookmarkRepo = new BookmarkRepository();
        $userRepo = new UserRepository();
        if ($bookmarkRepo -> findBookmark(getUrl(1), $response -> getSession('user')['id']))
            return true;
        else if ($userRepo -> createGrantRepo() -> one()
        -> where('user_id', equals($response -> getSession('user')['id']))
        -> where('block_id', equals(getUrl(1)))
        -> go() != null)
            return true;
        else
            return false;
    }
}