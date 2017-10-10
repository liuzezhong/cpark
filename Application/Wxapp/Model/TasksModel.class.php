<?php

/**
 * Created by PhpStorm.
 * User: PC41
 * Date: 2017-07-12
 * Time: 17:18
 */
namespace Wxapp\Model;
use Think\Model;

class TasksModel extends Model {
    private $_db = '';

    public function __construct() {
        $this->_db = M('tasks');
    }

    public function addOneTasks($data = array()) {
        if(!$data || !is_array($data)) {
            throw_exception('数据为空！');
        }
        return $this->_db->add($data);
    }

    public function getOneTasksByID($tasks_id = 0) {

        return $this->_db->where('tasks_id = ' . $tasks_id)->find();
    }

    public function updateOneTasks($tasks_id = 0, $data = array()) {

        return $this->_db->where('tasks_id = ' . $tasks_id)->save($data);
    }

    public function getAllTasks() {
        return $this->_db->where('tasks_status != -2')->order('tasks_id desc')->select();
    }

    public function addViewTimes($tasks_id = 0) {
        if(!$tasks_id) {
            throw_exception('任务ID为空！');
        }
        return $this->_db->where('tasks_id = ' . $tasks_id)->setInc('view_times');
    }

}