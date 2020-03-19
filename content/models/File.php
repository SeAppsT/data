<?php


namespace modules\content\models;


use Core\ORM\Model;

class File extends Model{
    protected $table = 'files';

    public $version_contents_id;
    public $name;
    public $type;
}