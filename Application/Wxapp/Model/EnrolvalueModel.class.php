<?php
/**
 * Created by PhpStorm.
 * User: PC41
 * Date: 2017-07-19
 * Time: 16:22
 */

namespace Wxapp\Model;


use Think\Model;

class EnrolvalueModel extends Model {
    private $_db = '';

    public function __construct() {
        $this->_db = M('enrol_value');
    }

    public function  addOneEnrolValue($data = array()) {
        if(!$data || !is_array($data)) {
            throw_exception('数据不存在！');
        }
        return $this->_db->add($data);
    }

    public function selectEnrolByEnrolID($enrol_id = 0) {
        return $this->_db->where('enrol_id = ' . $enrol_id)->select();
    }

    public function updateEnrolByEnrolID($enrol_id = 0,$name_id = 0,$data = array()) {
        $selectData['enrol_id'] = $enrol_id;
        $selectData['name_id'] = $name_id;
        $res = $this->_db->where($selectData)->save($data);
        return $res;
    }

    public function selectEnrolValueByEnrolID($enrol_ids = array()) {
        if(!$enrol_ids || !is_array($enrol_ids)) {
            throw_exception('数据不存在');
        }
        $selectData['enrol_id'] = array('IN',$enrol_ids);
        return $this->_db->where($selectData)->select();
    }
}