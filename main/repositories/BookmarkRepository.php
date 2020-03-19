<?php

namespace modules\main\repositories;

use Core\ORM\Repository;
use modules\main\models\Bookmark;

class BookmarkRepository extends Repository{
    protected $model = Bookmark::class;

    public function findBookmark($block_id, $user_id){
        return $this -> one()
            -> where('user_id', equals($user_id))
            -> where('block_id', equals($block_id))
            -> go();
    }
}