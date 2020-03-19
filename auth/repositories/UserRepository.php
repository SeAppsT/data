<?php


namespace auth\repositories;


use Core\Config;
use Core\Management\Response;
use Core\ORM\Repository;
use modules\auth\models\Grant;
use modules\auth\models\Ip;
use modules\auth\models\Privilege;
use modules\auth\models\User;
use modules\block\models\Block;
use modules\block\repositories\BlockRepository;

class UserRepository extends Repository {
    protected $model = User::class;

    public function isAuth(User $user){
        $user = $this -> one()
            -> where('code', equals($user -> code))
            -> where('active', equals(1))
            -> go();
        if ($user == null)
            return false;
        else
            return $user;
    }

    public function setGrants(Block $block, Response $response){
        $userRepo = new UserRepository();
        $_SESSION['user']['grants'] = null;
        $_SESSION['block_id'] = $block -> id;
        $block -> grants = $userRepo -> createGrantRepo()
            -> get()
            -> where('block_id', in($block -> id, 1))
            -> where('user_id', equals($response -> getSession('user')['id']))
            -> foreignKey('privilege_id', $userRepo -> createPrivilegeRepo()
                -> one(), 'id')
            -> go();
        foreach ($block -> grants as $grant){
            if ($grant -> getAccessLevel() != 0)
                $_SESSION['user']['grants'][$grant -> privilege_id -> name] = $grant -> getAccessLevel();
            else
                $_SESSION['user']['grants'][$grant -> privilege_id -> name] = 10;
        }
    }

    public function createGrantRepo(){
        $grant = new Grant();
        $repo = new Repository($grant);
        return $repo;
    }

    public function createPrivilegeRepo(){
        $grant = new Privilege();
        $repo = new Repository($grant);
        return $repo;
    }

    public function createFullGrants(int $user_id, int $block_id, int $access_level){
        $privilegies = $this -> createPrivilegeRepo() -> get() -> where('type', equals('public')) -> go();
        $mapper = Config::getMapper('main');
        foreach ($privilegies as $privilege){
            $grant = new Grant();
            $grant -> setUserId($user_id);
            $grant -> setBlockId($block_id);
            $grant -> setAccessLevel($access_level);
            $grant -> setPrivilegeId($privilege -> id);
            $mapper -> insert($grant);
        }
    }

    public function createGrants(Grant $grant, Block $block){
        $blockRepo = new BlockRepository();
        $arrayBlocks[] = array($block);
        // full block
        while (!empty(last_element($arrayBlocks))){
            // first abstraction level
            //print_r(last_element($arrayBlocks));
            foreach (last_element($arrayBlocks) as $oneBlock) {
                // one block
                $grant -> block_id = $oneBlock -> id;
                Config::getMapper('main') -> insert($grant);
                $arrayBlocks[] = $blockRepo -> get()
                    -> where('parent_id', equals($oneBlock -> id))
                    -> where('access_needed', in(1, 2))
                    -> go();
            }
        }
    }

    public function deleteGrants(Block $block, int $user_id){
        $userRepo = new UserRepository();
        $blockRepo = new BlockRepository();
        $arrayBlocks[] = array($block);
        // full block
        while (!empty(last_element($arrayBlocks))){
            // first abstraction level
            foreach (last_element($arrayBlocks) as $oneBlock) {
                // one block
                $userRepo -> createGrantRepo()
                    -> del()
                    -> where('user_id', equals($user_id))
                    -> where('block_id', equals($oneBlock -> id))
                    -> go();

                $arrayBlocks[] = $blockRepo -> get()
                    -> where('parent_id', equals($oneBlock -> id))
                    -> where('access_needed', in(1, 2))
                    -> go();
            }
            //print_r(last_element($arrayBlocks));
        }
    }

    public function deleteBlockGrant(int $block_id, int $user_id){
        $userRepo = new UserRepository();
        $g = $userRepo -> createGrantRepo()
            -> del()
            -> where('user_id', equals($user_id))
            -> where('block_id', equals($block_id))
            -> go();
    }

    public function createBlockGrant(Grant $grant){
        Config::getMapper('main') -> insert($grant);
    }

    public function getPrivilegeId($name){
        return $this -> createPrivilegeRepo()
            -> one()
            -> where('name', equals($name))
            -> go() -> id;
    }

    public function maxPrivilegeLevel($privilege_name){
        $num = 3;
        switch ($privilege_name){
            case 'lookBlock': $num = 3;
            break;
            case 'addBlock': $num = 3;
            break;
            case 'editBlock': $num = 0;
            break;
            case 'delBlock': $num = 0;
            break;
            case 'addContent': $num = 3;
            break;
            case 'editContent': $num = 3;
            break;
            case 'delContent': $num = 3;
            break;
            case 'seeGrants': $num = 0;
            break;
            case 'setGrants': $num = 3;
            break;
        }
        return $num;
    }

    public function setBaseGrants(int $user_id, int $block_id){
        $grant = new \modules\auth\models\Grant();
        $grant -> user_id = $user_id;
        $grant -> privilege_id = $this -> getPrivilegeId('lookBlock');
        $grant -> block_id = $block_id;
        $grant -> access_level = 1;
        \Core\Config::getMapper('main') -> insert($grant);
        $grant = new \modules\auth\models\Grant();
        $grant -> user_id = $user_id;
        $grant -> privilege_id = $this -> getPrivilegeId('addBlock');
        $grant -> block_id = $block_id;
        $grant -> access_level = $this -> maxPrivilegeLevel('addBlock');
        \Core\Config::getMapper('main') -> insert($grant);
    }

    public function delUser($user_id){
        $this -> upd() -> set('active', 0) -> where('id', equals($user_id)) -> go();
    }

    public function getIps($user_id){
        $repo = new Repository(new Ip());
        return $repo -> get()
            -> where('user_id', equals($user_id))
            -> go();
    }

    public function saveIp($user_id){
        $ip = new Ip();
        $ip -> ip = $_SERVER['HTTP_X_REAL_IP'];
        $ip -> user_id = $user_id;
        Config::getMapper('main') -> insert($ip);
    }

    public function searchAndInsertIp($user_id){
        $ips = $this -> getIps($user_id);
        if (!empty($ips)) {
            foreach ($ips as $ip) {
                if ($ip -> ip == $_SERVER['HTTP_X_REAL_IP']) {
                    //$ip -> date = date('Y-d-m H:i:S');
                    Config::getMapper('main')->update($ip);
                    return true;
                }
            }
        }
        $this -> saveIp($user_id);
        return true;
    }
}