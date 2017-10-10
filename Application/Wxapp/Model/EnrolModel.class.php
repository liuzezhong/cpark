<?php
/**
 * Created by PhpStorm.
 * User: PC41
 * Date: 2017-09-29
 * Time: 10:07
 */

namespace Wxapp\Model;
use Think\Model;

class EnrolModel extends Model
{
    private $_db = '';
    public function __construct() {
        $this->_db = M('enrol');
    }

    public function getEnrolByEnrolID($enrol_id = 0) {
        if(!$enrol_id || $enrol_id == 0) {
            throw_exception('EnrolModel getEnrolByEnrolID enrol_id is null');
        }
        return $this->_db->where('enrol_id = ' . $enrol_id)->find();
    }

    public function setEnrolSignTime($enrol_id) {
        if(!$enrol_id) {
            throw_exception('EnrolModel setEnrolSignTime enrol_id is null');
        }
        $enrolArray = array(
            'sign_time' => time(),
        );
        return $this->_db->where('enrol_id = ' . $enrol_id)->save($enrolArray);
    }
}