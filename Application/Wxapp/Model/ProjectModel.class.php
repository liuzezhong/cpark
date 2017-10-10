<?php
/**
 * Created by PhpStorm.
 * User: PC41
 * Date: 2017-07-13
 * Time: 17:02
 */

namespace Wxapp\Model;


use Think\Model;

class ProjectModel extends Model {
    private $_db = '';

    public function __construct() {
        $this->_db = M('project');
    }

    public function addOneProject($data = array()) {
        if(!$data || !is_array($data)) {
            throw_exception('数据为空！');
        }
        return $this->_db->add($data);
    }

    public function selectProjectByTasksID($tasks_id = 0) {
        if($tasks_id ==0) {
            throw_exception('任务ID为空！');
        }
        return $this->_db->where('tasks_id = ' . $tasks_id)->select();
    }

    public function updateProjectByID($project_id = 0,$data = array()) {
        if(!$project_id || !is_numeric($project_id)) {
            throw_exception('项目ID为空！');
        }
        if(!$data || !is_array($data)) {
            throw_exception('数据为空！');
        }
        return $this->_db->where('project_id = ' . $project_id)->save($data);
    }

    public function deleteOneProjectByID($project_id = 0) {
        return $this->_db->where('project_id = ' . $project_id)->delete();
    }

    public function getOneProjectByID($project_id = 0) {
        return $this->_db->where('project_id = ' . $project_id)->find();
    }
}