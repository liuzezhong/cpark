<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2017/9/28
 * Time: 15:41
 */

namespace Wxapp\Model;


use Think\Model;

class ScanuserModel extends Model
{
    private $_db = '';

    public function __construct() {
        $this->_db = M('scanuser');
    }

    public function addScanuser($data = array()) {
        if(!$data || !is_array($data)) {
            throw_exception('ScanuserModel addScanuser data is null !');
        }

        return $this->_db->add($data);
    }

    public function findScanuser($openid = '') {
        if(!$openid) {
            throw_exception('ScanuserModel findScanuser openid is null !');
        }

        return $this->_db->where('openid = ' . "'".$openid."'")->find();
    }

    public function updateUser($user_id = 0 ,$data = array()) {
        if(!$data || !is_array($data)) {
            throw_exception('ScanuserModel updateUser data is null !');
        }
        if(!$user_id) {
            throw_exception('ScanuserModel updateUser user_id is null !');
        }
        return $this->_db->where('user_id = '.$user_id)->save($data);
    }
}