<?php


//namespace Main\Controllers;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use auth\repositories\UserRepository;
use modules\auth\models\User;
class AuthCtrl extends Controller{

    protected $path = 'auth/login';
    protected $access = modules\auth\accesses\NoAuthAcs::class;

    public function stand(Request $request, Response $response){
        return $response -> page('login');
    }

    public function action(Request $request, Response $response){
        $userRepo = new UserRepository();
        $user = new User();
        if ($_SESSION['authError'] < 3){
            $user -> code = $request -> getPostParam('code');
            $user = $userRepo -> isAuth($user);
            if ($user){
                $userRepo -> searchAndInsertIp($user -> id);
                $response -> addSession('user', ['id' => $user -> id, 'name' => $user -> name, 'code' => $user -> code]);
                header('Location: /home');
            }
            else {
                $error = 'Такого пользователя нет в базе или он неактивен. <br /> У вас осталось еще '.(3 - $_SESSION['authError']).' попытк'.($_SESSION['authError'] == 1 ? 'а' : 'и');
                $_SESSION['authError'] += 1;
                return $response -> page('login', ['error' => $error]);
            }
        } else {
            $error = 'Ваши попытки кончились. Дождитесь окончания сессии или свяжитесь с разработчиком.';
            return $response -> page('login', ['error' => $error]);
        }
    }
}