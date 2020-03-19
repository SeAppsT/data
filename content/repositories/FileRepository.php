<?php


namespace modules\content\repositories;


use Core\Config;
use Core\ORM\Repository;
use modules\content\models\File;

class FileRepository extends Repository{
    protected $model = File::class;

    public function save($file, $versionContentId, $dir = 'user_contents/'){
        $name = md5($file['name']).$file['name'];
        $path = 'static/'.$dir.$name;
        $filesOld = $this -> get()
            -> where('name', equals('/'.$path))
            -> where('version_contents_id', equals($versionContentId))
            -> go();
        if ($filesOld == null)
            copy($file['tmp_name'], $path);
        return '/'.$path;
    }

    public function saveFiles($version_contents_id){
        $images = [];
        if ($_FILES['files']['name'][0] != '') {
            for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
                $images[] =
                    ['name' => $_FILES['files']['name'][$i],
                        'type' => $_FILES['files']['type'][$i],
                        'tmp_name' => $_FILES['files']['tmp_name'][$i]
                    ];
            }
        }
        foreach ($images as $imageUpl){
            $image = new \modules\content\models\File();
            $image -> name = $this -> save($imageUpl, $version_contents_id, 'files/user_contents/');
            $image -> version_contents_id = $version_contents_id;
            $image -> type = $imageUpl['type'];
            Config::getMapper('main') -> insert($image);
        }
    }

    public function insertLastFiles($version_contents_id){
        if (!empty($_POST['files1'])) {
            foreach ($_POST['files1'] as $image) {
                $file = new File();
                $file -> name = $image['name'];
                $file -> type = $image['type'];
                $file -> version_contents_id = $version_contents_id;
                Config::getMapper('main') -> insert($file);
            }
        }
    }
}