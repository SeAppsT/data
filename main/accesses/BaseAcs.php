<?php


namespace modules\main\accesses;


use auth\repositories\UserRepository;
use modules\block\models\Content;
use modules\block\repositories\BlockRepository;

class BaseAcs{
    public function isAuth(){
        if (isAuth())
            return true;
        else
            header('Location: /auth/login');
    }
    
     public function noAuth(){
        if (isAuth())
            return false;
        else
            return true;
    }

    public function hasGrant($grant, $block_id, $index = 0){
        $userRepo = new UserRepository();
        $grant = $userRepo -> createGrantRepo()
            -> one()
            -> where('privilege_id', equals($userRepo -> getPrivilegeId($grant)))
            -> where('block_id', equals($block_id))
            -> where('user_id', equals($_SESSION['user']['id']))
            -> where('access_level', bigEq($index))
            -> go();
        if ($grant != null)
            return true;
        else
            return false;
    }

    public function hasPriv($privName, $index = 1){
        $blockRepo = new BlockRepository();
        $content = $blockRepo -> createContentRepo()
            -> one('block_id')
            -> where('id', equals(getUrl(2)))
            -> go();
        if (hasPriv($privName, $index) && ($content -> block_id == $_SESSION['block_id'] || getUrl(2) == $_SESSION['block_id']))
            return true;
        else
            return false;
    }

    public function hasDelAccessToContent(Content $content){
        $entity = $content -> version_contents -> user_id == '' ? $content -> user_id : $content -> version_contents -> user_id;
        if ($this -> hasPriv('delContent', 3)
            || (($entity == $_SESSION['user']['id']) && $this -> hasPriv('delContent'))
            || ($this -> hasPriv('delContent', 2) && $content -> version_contents -> importance <= 2))
            return true;
        else
            return false;
    }

    public function hasEditAccessToContent(Content $content){
        $entity = $content -> version_contents -> user_id == '' ? $content -> user_id : $content -> version_contents -> user_id;
        if ($this -> hasPriv('editContent', 3)
            || (($entity == $_SESSION['user']['id']) && $this -> hasPriv('editContent', 1))
            || ($this -> hasPriv('editContent', 2) && $content -> version_contents -> importance <= 2))
            return true;
        else
            return false;
    }

}