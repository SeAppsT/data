<?php


namespace modules\main\models;


use Core\ORM\Model;

class EventId extends Model{
    protected $table = 'event_ids';
    public $entity_id;
    public $entity_table;
    public $event_id;
}